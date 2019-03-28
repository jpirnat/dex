<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\Structs;

use Jp\Dex\Domain\Abilities\AbilityNameRepositoryInterface;
use Jp\Dex\Domain\Abilities\AbilityRepositoryInterface;
use Jp\Dex\Domain\Abilities\PokemonAbilityRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Versions\GenerationId;

class DexPokemonAbilityFactory
{
	/** @var PokemonAbilityRepositoryInterface $pokemonAbilityRepository */
	private $pokemonAbilityRepository;

	/** @var AbilityRepositoryInterface $abilityRepository */
	private $abilityRepository;

	/** @var AbilityNameRepositoryInterface $abilityNameRepository */
	private $abilityNameRepository;

	/**
	 * Constructor.
	 *
	 * @param PokemonAbilityRepositoryInterface $pokemonAbilityRepository
	 * @param AbilityRepositoryInterface $abilityRepository
	 * @param AbilityNameRepositoryInterface $abilityNameRepository
	 */
	public function __construct(
		PokemonAbilityRepositoryInterface $pokemonAbilityRepository,
		AbilityRepositoryInterface $abilityRepository,
		AbilityNameRepositoryInterface $abilityNameRepository
	) {
		$this->pokemonAbilityRepository = $pokemonAbilityRepository;
		$this->abilityRepository = $abilityRepository;
		$this->abilityNameRepository = $abilityNameRepository;
	}

	/**
	 * Get the dex Pokémon abilities for this Pokémon.
	 *
	 * @param GenerationId $generationId
	 * @param PokemonId $pokemonId
	 * @param LanguageId $languageId
	 *
	 * @return DexPokemonAbility[]
	 */
	public function getByPokemon(
		GenerationId $generationId,
		PokemonId $pokemonId,
		LanguageId $languageId
	) : array {
		$pokemonAbilities = $this->pokemonAbilityRepository->getByGenerationAndPokemon(
			$generationId,
			$pokemonId
		);

		$abilities = [];

		foreach ($pokemonAbilities as $pokemonAbility) {
			$ability = $this->abilityRepository->getById($pokemonAbility->getAbilityId());
			$abilityName = $this->abilityNameRepository->getByLanguageAndAbility(
				$languageId,
				$ability->getId()
			);

			$abilities[] = new DexPokemonAbility(
				$ability->getIdentifier(),
				$abilityName->getName(),
				$pokemonAbility->isHiddenAbility()
			);
		}

		return $abilities;
	}
}
