<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\DexPokemon;

use Jp\Dex\Application\Models\VersionGroupModel;
use Jp\Dex\Domain\Abilities\DexAbilityRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\PokemonNameRepositoryInterface;
use Jp\Dex\Domain\Pokemon\PokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\DexStatRepositoryInterface;

final class DexPokemonModel
{
	private array $pokemon = [];
	private array $baseStats = [];
	private array $abilities = [];


	public function __construct(
		private readonly VersionGroupModel $versionGroupModel,
		private readonly PokemonRepositoryInterface $pokemonRepository,
		private readonly PokemonNameRepositoryInterface $pokemonNameRepository,
		private readonly DexStatRepositoryInterface $dexStatRepository,
		private readonly DexAbilityRepositoryInterface $dexAbilityRepository,
		private readonly DexPokemonMatchupsModel $dexPokemonMatchupsModel,
		private readonly DexPokemonEvolutionsModel $dexPokemonEvolutionsModel,
		private readonly DexPokemonMovesModel $dexPokemonMovesModel,
	) {}


	/**
	 * Set data for the dex Pokémon page.
	 */
	public function setData(
		string $vgIdentifier,
		string $pokemonIdentifier,
		LanguageId $languageId,
	) : void {
		$versionGroupId = $this->versionGroupModel->setByIdentifier($vgIdentifier);

		$pokemon = $this->pokemonRepository->getByIdentifier($pokemonIdentifier);
		$pokemonName = $this->pokemonNameRepository->getByLanguageAndPokemon(
			$languageId,
			$pokemon->getId(),
		);
		$this->pokemon = [
			'identifier' => $pokemon->getIdentifier(),
			'name' => $pokemonName->getName(),
		];

		$this->baseStats = $this->dexStatRepository->getBaseStats(
			$versionGroupId,
			$pokemon->getId(),
			$languageId,
		);

		// Set generations for the generation control.
		$this->versionGroupModel->setWithPokemon($pokemon->getId());

		// Set the Pokémon's abilities.
		if ($versionGroupId->hasAbilities()) {
			$this->abilities = $this->dexAbilityRepository->getByPokemon(
				$versionGroupId,
				$pokemon->getId(),
				$languageId,
			);
		}

		// Set the Pokémon's matchups.
		$this->dexPokemonMatchupsModel->setData(
			$this->versionGroupModel->getVersionGroup(),
			$pokemon->getId(),
			$languageId,
			$this->abilities,
		);

		// Set the Pokémon's evolutions.
		$this->dexPokemonEvolutionsModel->setData(
			$versionGroupId,
			$pokemon->getId(),
			$languageId,
		);

		$this->dexPokemonMovesModel->setData(
			$versionGroupId,
			$pokemon->getId(),
			$languageId,
		);
	}


	public function getVersionGroupModel() : VersionGroupModel
	{
		return $this->versionGroupModel;
	}

	public function getPokemon() : array
	{
		return $this->pokemon;
	}

	public function getBaseStats() : array
	{
		return $this->baseStats;
	}

	public function getAbilities() : array
	{
		return $this->abilities;
	}

	public function getDexPokemonMatchupsModel() : DexPokemonMatchupsModel
	{
		return $this->dexPokemonMatchupsModel;
	}

	public function getDexPokemonEvolutionsModel() : DexPokemonEvolutionsModel
	{
		return $this->dexPokemonEvolutionsModel;
	}

	public function getDexPokemonMovesModel() : DexPokemonMovesModel
	{
		return $this->dexPokemonMovesModel;
	}
}
