<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\DexPokemon;

use Jp\Dex\Application\Models\GenerationModel;
use Jp\Dex\Domain\Abilities\DexAbilityRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\PokemonNameRepositoryInterface;
use Jp\Dex\Domain\Pokemon\PokemonRepositoryInterface;
use Jp\Dex\Domain\Versions\DexVersionGroup;
use Jp\Dex\Domain\Versions\DexVersionGroupRepositoryInterface;

final class DexPokemonModel
{
	private array $pokemon = [];
	private array $abilities = [];

	/** @var DexVersionGroup[] $versionGroups */
	private array $versionGroups = [];


	public function __construct(
		private GenerationModel $generationModel,
		private PokemonRepositoryInterface $pokemonRepository,
		private PokemonNameRepositoryInterface $pokemonNameRepository,
		private DexAbilityRepositoryInterface $dexAbilityRepository,
		private DexPokemonMatchupsModel $dexPokemonMatchupsModel,
		private DexVersionGroupRepositoryInterface $dexVgRepository,
		private DexPokemonMovesModel $dexPokemonMovesModel,
	) {}


	/**
	 * Set data for the dex Pokémon page.
	 *
	 * @param string $generationIdentifier
	 * @param string $pokemonIdentifier
	 * @param LanguageId $languageId
	 *
	 * @return void
	 */
	public function setData(
		string $generationIdentifier,
		string $pokemonIdentifier,
		LanguageId $languageId
	) : void {
		$generationId = $this->generationModel->setByIdentifier($generationIdentifier);

		$pokemon = $this->pokemonRepository->getByIdentifier($pokemonIdentifier);
		$pokemonName = $this->pokemonNameRepository->getByLanguageAndPokemon(
			$languageId,
			$pokemon->getId()
		);
		$this->pokemon = [
			'identifier' => $pokemon->getIdentifier(),
			'name' => $pokemonName->getName(),
		];

		// Set generations for the generation control.
		$this->generationModel->setWithPokemon($pokemon->getId());

		// Set the Pokémon's abilities.
		$this->abilities = $this->dexAbilityRepository->getByPokemon(
			$generationId,
			$pokemon->getId(),
			$languageId
		);

		// Set the Pokémon's matchups.
		$this->dexPokemonMatchupsModel->setData(
			$generationId,
			$pokemon->getId(),
			$languageId,
			$this->abilities
		);

		// Get the version groups this Pokémon has appeared in.
		$this->versionGroups = $this->dexVgRepository->getWithPokemon(
			$pokemon->getId(),
			$languageId,
			$generationId
		);

		// Get the generation the Pokémon was introduced in.
		$key = array_key_first($this->versionGroups);
		$introducedInVg = $this->versionGroups[$key];

		$this->dexPokemonMovesModel->setData(
			$pokemon->getId(),
			$introducedInVg->getGenerationId(),
			$generationId,
			$languageId,
			$this->versionGroups
		);
	}


	/**
	 * Get the generation model.
	 *
	 * @return GenerationModel
	 */
	public function getGenerationModel() : GenerationModel
	{
		return $this->generationModel;
	}

	/**
	 * Get the Pokémon.
	 *
	 * @return array
	 */
	public function getPokemon() : array
	{
		return $this->pokemon;
	}

	/**
	 * Get the abilities.
	 *
	 * @return array
	 */
	public function getAbilities() : array
	{
		return $this->abilities;
	}

	/**
	 * Get the dex Pokémon matchups model.
	 *
	 * @return DexPokemonMatchupsModel
	 */
	public function getDexPokemonMatchupsModel() : DexPokemonMatchupsModel
	{
		return $this->dexPokemonMatchupsModel;
	}

	/**
	 * Get the version groups.
	 *
	 * @return DexVersionGroup[]
	 */
	public function getVersionGroups() : array
	{
		return $this->versionGroups;
	}

	/**
	 * Get the dex Pokémon moves model.
	 *
	 * @return DexPokemonMovesModel
	 */
	public function getDexPokemonMovesModel() : DexPokemonMovesModel
	{
		return $this->dexPokemonMovesModel;
	}
}
