<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use Jp\Dex\Domain\Abilities\AbilityDescriptionRepositoryInterface;
use Jp\Dex\Domain\Abilities\AbilityNameRepositoryInterface;
use Jp\Dex\Domain\Abilities\AbilityRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\DexPokemon;
use Jp\Dex\Domain\Pokemon\DexPokemonRepositoryInterface;

final class DexAbilityModel
{
	private array $ability;
	private array $stats = [];

	/** @var DexPokemon[] $normalPokemon */
	private array $normalPokemon = [];

	/** @var DexPokemon[] $hiddenPokemon */
	private array $hiddenPokemon = [];


	public function __construct(
		private GenerationModel $generationModel,
		private AbilityRepositoryInterface $abilityRepository,
		private AbilityNameRepositoryInterface $abilityNameRepository,
		private AbilityDescriptionRepositoryInterface $abilityDescriptionRepository,
		private StatNameModel $statNameModel,
		private DexPokemonRepositoryInterface $dexPokemonRepository,
	) {}


	/**
	 * Set data for the dex ability page.
	 */
	public function setData(
		string $generationIdentifier,
		string $abilityIdentifier,
		LanguageId $languageId
	) : void {
		$generationId = $this->generationModel->setByIdentifier($generationIdentifier);

		$ability = $this->abilityRepository->getByIdentifier($abilityIdentifier);

		$this->generationModel->setGensSinceVg($ability->getIntroducedInVersionGroupId());

		$abilityName = $this->abilityNameRepository->getByLanguageAndAbility(
			$languageId,
			$ability->getId()
		);

		$abilityDescription = $this->abilityDescriptionRepository->getByGenerationAndLanguageAndAbility(
			$generationId,
			$languageId,
			$ability->getId()
		);

		$this->ability = [
			'identifier' => $ability->getIdentifier(),
			'name' => $abilityName->getName(),
			'description' => $abilityDescription->getDescription(),
		];

		// Get stat name abbreviations.
		$this->stats = $this->statNameModel->getByGeneration($generationId, $languageId);

		// Get Pokémon with this ability.
		$pokemons = $this->dexPokemonRepository->getWithAbility(
			$generationId,
			$ability->getId(),
			$languageId
		);
		$this->normalPokemon = [];
		$this->hiddenPokemon = [];
		foreach ($pokemons as $pokemon) {
			$pokemonAbility = $pokemon->getAbilities()[$ability->getId()->value()];
			if (!$pokemonAbility->isHiddenAbility()) {
				$this->normalPokemon[] = $pokemon;
			} else {
				$this->hiddenPokemon[] = $pokemon;
			}
		}
	}


	/**
	 * Get the generation model.
	 */
	public function getGenerationModel() : GenerationModel
	{
		return $this->generationModel;
	}

	/**
	 * Get the ability.
	 */
	public function getAbility() : array
	{
		return $this->ability;
	}

	/**
	 * Get the stats and their names.
	 */
	public function getStats() : array
	{
		return $this->stats;
	}

	/**
	 * Get the Pokémon that have this ability as a normal ability.
	 *
	 * @return DexPokemon[]
	 */
	public function getNormalPokemon() : array
	{
		return $this->normalPokemon;
	}

	/**
	 * Get the Pokémon that have this ability as a hidden ability.
	 *
	 * @return DexPokemon[]
	 */
	public function getHiddenPokemon() : array
	{
		return $this->hiddenPokemon;
	}
}
