<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\DexPokemon;

use Jp\Dex\Application\Models\VersionGroupModel;
use Jp\Dex\Domain\Abilities\DexAbilityRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\PokemonNameRepositoryInterface;
use Jp\Dex\Domain\Pokemon\PokemonRepositoryInterface;

final class DexPokemonModel
{
	private array $pokemon = [];
	private array $abilities = [];


	public function __construct(
		private VersionGroupModel $versionGroupModel,
		private PokemonRepositoryInterface $pokemonRepository,
		private PokemonNameRepositoryInterface $pokemonNameRepository,
		private DexAbilityRepositoryInterface $dexAbilityRepository,
		private DexPokemonMatchupsModel $dexPokemonMatchupsModel,
		private DexPokemonEvolutionsModel $dexPokemonEvolutionsModel,
		private DexPokemonMovesModel $dexPokemonMovesModel,
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

		// Set generations for the generation control.
		$this->versionGroupModel->setWithPokemon($pokemon->getId());

		// Set the Pokémon's abilities.
		$this->abilities = $this->dexAbilityRepository->getByPokemon(
			$versionGroupId,
			$pokemon->getId(),
			$languageId,
		);

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


	/**
	 * Get the version group model.
	 */
	public function getVersionGroupModel() : VersionGroupModel
	{
		return $this->versionGroupModel;
	}

	/**
	 * Get the Pokémon.
	 */
	public function getPokemon() : array
	{
		return $this->pokemon;
	}

	/**
	 * Get the abilities.
	 */
	public function getAbilities() : array
	{
		return $this->abilities;
	}

	/**
	 * Get the dex Pokémon matchups model.
	 */
	public function getDexPokemonMatchupsModel() : DexPokemonMatchupsModel
	{
		return $this->dexPokemonMatchupsModel;
	}

	/**
	 * Get the dex Pokémon evolutions model.
	 */
	public function getDexPokemonEvolutionsModel() : DexPokemonEvolutionsModel
	{
		return $this->dexPokemonEvolutionsModel;
	}

	/**
	 * Get the dex Pokémon moves model.
	 */
	public function getDexPokemonMovesModel() : DexPokemonMovesModel
	{
		return $this->dexPokemonMovesModel;
	}
}
