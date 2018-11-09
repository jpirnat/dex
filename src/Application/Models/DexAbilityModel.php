<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use Jp\Dex\Application\Models\Structs\DexPokemon;
use Jp\Dex\Application\Models\Structs\DexPokemonFactory;
use Jp\Dex\Domain\Abilities\AbilityDescriptionRepositoryInterface;
use Jp\Dex\Domain\Abilities\AbilityNameRepositoryInterface;
use Jp\Dex\Domain\Abilities\AbilityRepositoryInterface;
use Jp\Dex\Domain\Abilities\PokemonAbilityRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;

class DexAbilityModel
{
	/** @var GenerationModel $generationModel */
	private $generationModel;

	/** @var DexPokemonFactory $dexPokemonFactory */
	private $dexPokemonFactory;

	/** @var AbilityRepositoryInterface $abilityRepository */
	private $abilityRepository;

	/** @var AbilityNameRepositoryInterface $abilityNameRepository */
	private $abilityNameRepository;

	/** @var AbilityDescriptionRepositoryInterface $abilityDescriptionRepository */
	private $abilityDescriptionRepository;

	/** @var PokemonAbilityRepositoryInterface $pokemonAbilityRepository */
	private $pokemonAbilityRepository;


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
	 * @param DexPokemonFactory $dexPokemonFactory
	 * @param AbilityRepositoryInterface $abilityRepository
	 * @param AbilityNameRepositoryInterface $abilityNameRepository
	 * @param AbilityDescriptionRepositoryInterface $abilityDescriptionRepository
	 * @param PokemonAbilityRepositoryInterface $pokemonAbilityRepository
	 */
	public function __construct(
		GenerationModel $generationModel,
		DexPokemonFactory $dexPokemonFactory,
		AbilityRepositoryInterface $abilityRepository,
		AbilityNameRepositoryInterface $abilityNameRepository,
		AbilityDescriptionRepositoryInterface $abilityDescriptionRepository,
		PokemonAbilityRepositoryInterface $pokemonAbilityRepository
	) {
		$this->generationModel = $generationModel;
		$this->dexPokemonFactory = $dexPokemonFactory;
		$this->abilityRepository = $abilityRepository;
		$this->abilityNameRepository = $abilityNameRepository;
		$this->abilityDescriptionRepository = $abilityDescriptionRepository;
		$this->pokemonAbilityRepository = $pokemonAbilityRepository;
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
		$generationId = $this->generationModel->setGeneration($generationIdentifier);

		$ability = $this->abilityRepository->getByIdentifier($abilityIdentifier);

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
		$pokemonAbilities = $this->pokemonAbilityRepository->getByGenerationAndAbility(
			$generationId,
			$ability->getId()
		);
		$this->normalPokemon = [];
		$this->hiddenPokemon = [];
		foreach ($pokemonAbilities as $pokemonAbility) {
			$dexPokemon = $this->dexPokemonFactory->getDexPokemon(
				$generationId,
				$pokemonAbility->getPokemonId(),
				$languageId
			);

			if (!$pokemonAbility->isHiddenAbility()) {
				$this->normalPokemon[] = $dexPokemon;
			} else {
				$this->hiddenPokemon[] = $dexPokemon;
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
