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
	/** @var GenerationModel $generationModel */
	private $generationModel;

	/** @var AbilityRepositoryInterface $abilityRepository */
	private $abilityRepository;

	/** @var AbilityNameRepositoryInterface $abilityNameRepository */
	private $abilityNameRepository;

	/** @var AbilityDescriptionRepositoryInterface $abilityDescriptionRepository */
	private $abilityDescriptionRepository;

	/** @var DexPokemonRepositoryInterface $dexPokemonRepository */
	private $dexPokemonRepository;


	/** @var array $ability */
	private $ability;

	/** @var DexPokemon[] $normalPokemon */
	private $normalPokemon;

	/** @var DexPokemon[] $hiddenPokemon */
	private $hiddenPokemon;


	/**
	 * Constructor.
	 *
	 * @param GenerationModel $generationModel
	 * @param AbilityRepositoryInterface $abilityRepository
	 * @param AbilityNameRepositoryInterface $abilityNameRepository
	 * @param AbilityDescriptionRepositoryInterface $abilityDescriptionRepository
	 * @param DexPokemonRepositoryInterface $dexPokemonRepository
	 */
	public function __construct(
		GenerationModel $generationModel,
		AbilityRepositoryInterface $abilityRepository,
		AbilityNameRepositoryInterface $abilityNameRepository,
		AbilityDescriptionRepositoryInterface $abilityDescriptionRepository,
		DexPokemonRepositoryInterface $dexPokemonRepository
	) {
		$this->generationModel = $generationModel;
		$this->abilityRepository = $abilityRepository;
		$this->abilityNameRepository = $abilityNameRepository;
		$this->abilityDescriptionRepository = $abilityDescriptionRepository;
		$this->dexPokemonRepository = $dexPokemonRepository;
	}

	/**
	 * Set data for the dex ability page.
	 *
	 * @param string $generationIdentifier
	 * @param string $abilityIdentifier
	 * @param LanguageId $languageId
	 *
	 * @return void
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
	 *
	 * @return GenerationModel
	 */
	public function getGenerationModel() : GenerationModel
	{
		return $this->generationModel;
	}

	/**
	 * Get the ability.
	 *
	 * @return array
	 */
	public function getAbility() : array
	{
		return $this->ability;
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
