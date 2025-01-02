<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\AdvancedPokemonSearch;

use Jp\Dex\Application\Models\VersionGroupModel;
use Jp\Dex\Domain\Abilities\AbilityNotFoundException;
use Jp\Dex\Domain\Abilities\AbilityRepositoryInterface;
use Jp\Dex\Domain\Abilities\AbilityTypeMatchups;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\DexPokemon;
use Jp\Dex\Domain\Pokemon\GenderRatio;
use Jp\Dex\Domain\Types\TypeMatchupRepositoryInterface;
use Jp\Dex\Domain\Types\TypeRepositoryInterface;
use Jp\Dex\Domain\Versions\VersionGroup;
use Jp\Dex\Domain\Versions\VersionGroupNotFoundException;

final class AdvancedPokemonSearchSubmitModel
{
	/** @var DexPokemon[] $pokemons */
	private(set) array $pokemons = [];


	public function __construct(
		private readonly VersionGroupModel $versionGroupModel,
		private readonly AbilityRepositoryInterface $abilityRepository,
		private readonly AdvancedPokemonSearchQueriesInterface $queries,
		private readonly TypeRepositoryInterface $typeRepository,
		private readonly TypeMatchupRepositoryInterface $typeMatchupRepository,
		private readonly AbilityTypeMatchups $abilityTypeMatchups,
	) {}


	/**
	 * Set data for the advanced Pokémon search page.
	 *
	 * @param string[] $typeIdentifiers
	 * @param string[][] $matchups
	 * @param string[] $eggGroupIdentifiers
	 * @param string[] $genderRatios
	 * @param string[] $moveIdentifiers
	 */
	public function setData(
		string $vgIdentifier,
		array $typeIdentifiers,
		string $typesOperator,
		array $matchups,
		string $includeAbilityMatchups,
		string $abilityIdentifier,
		array $eggGroupIdentifiers,
		string $eggGroupsOperator,
		array $genderRatios,
		string $genderRatiosOperator,
		array $moveIdentifiers,
		string $includeTransferMoves,
		LanguageId $languageId,
	) : void {
		try {
			$versionGroupId = $this->versionGroupModel->setByIdentifier($vgIdentifier);
		} catch (VersionGroupNotFoundException) {
			return;
		}

		$includeAbilityMatchups = (bool) $includeAbilityMatchups;
		$includeTransferMoves = (bool) $includeTransferMoves;

		$typeIdentifiersToIds = $this->queries->getTypeIdentifiersToIds();
		$eggGroupIdentifiersToIds = $this->queries->getEggGroupIdentifiersToIds();
		$moveIdentifiersToIds = $this->queries->getMoveIdentifiersToIds();

		$typeIds = [];
		foreach ($typeIdentifiers as $typeIdentifier) {
			if (isset($typeIdentifiersToIds[$typeIdentifier])) {
				$typeIds[] = $typeIdentifiersToIds[$typeIdentifier];
			}
		}

		$realMatchups = [];
		foreach ($matchups as $typeIdentifier => $comparisons) {
			if (isset($typeIdentifiersToIds[$typeIdentifier])) {
				foreach ($comparisons as $comparison) {
					$comparison = Comparison::tryFrom($comparison);
					if ($comparison) {
						$realMatchups[$typeIdentifier][] = $comparison;
					}
				}
			}
		}
		$matchups = $realMatchups;

		$abilityId = null;
		if ($abilityIdentifier) {
			try {
				$ability = $this->abilityRepository->getByIdentifier($abilityIdentifier);
				$abilityId = $ability->id;
			} catch (AbilityNotFoundException) {
			}
		}

		$eggGroupIds = [];
		foreach ($eggGroupIdentifiers as $eggGroupIdentifier) {
			if (isset($eggGroupIdentifiersToIds[$eggGroupIdentifier])) {
				$eggGroupIds[] = $eggGroupIdentifiersToIds[$eggGroupIdentifier];
			}
		}

		$originalGenderRatios = $genderRatios;
		$genderRatios = [];
		foreach ($originalGenderRatios as $genderRatio) {
			$genderRatios[] = new GenderRatio((int) $genderRatio);
		}

		$moveIds = [];
		foreach ($moveIdentifiers as $moveIdentifier) {
			if (isset($moveIdentifiersToIds[$moveIdentifier])) {
				$moveIds[] = $moveIdentifiersToIds[$moveIdentifier];
			}
		}

		$pokemons = $this->queries->search(
			$versionGroupId,
			$typeIds,
			$typesOperator,
			$abilityId,
			$eggGroupIds,
			$eggGroupsOperator,
			$genderRatios,
			$genderRatiosOperator,
			$moveIds,
			$includeTransferMoves,
			$languageId,
		);

		// The big database query can't handle all possible search criteria.
		// Process any further search criteria here.

		if ($matchups) {
			$pokemons = $this->filterByMatchups(
				$this->versionGroupModel->versionGroup,
				$pokemons,
				$matchups,
				$includeAbilityMatchups,
			);
		}

		$this->pokemons = $pokemons;
	}

	/**
	 * @param DexPokemon[] $pokemons
	 * @param Comparison[][] $matchups Indexed first by type identifier.
	 *
	 * @return DexPokemon[]
	 */
	private function filterByMatchups(
		VersionGroup $versionGroup,
		array $pokemons,
		array $matchups,
		bool $includeAbilityMatchups,
	) : array {
		// First, calculate all matchups for all Pokémon.

		/**
		 * @var float[][][] $pokemonAbilityTypeMultipliers
		 *     Indexed by Pokémon id, then by ability identifier, then by
		 *     attacking type identifier.
		 */
		$pokemonAbilityTypeMultipliers = [];
		$types = $this->typeRepository->getMainByVersionGroup($versionGroup->id);
		$multipliers = $this->typeMatchupRepository->getMultipliers($versionGroup->generationId);

		foreach ($pokemons as $pokemonId => $pokemon) {
			// Initialize matchups for each type.
			foreach ($types as $type) {
				$typeIdentifier = $type->identifier;
				$pokemonAbilityTypeMultipliers[$pokemonId]['none'][$typeIdentifier] = 1;
			}

			// Calculate this Pokémon's type matchups.
			foreach ($pokemon->types as $defendingType) {
				$defendingTypeIdentifier = $defendingType->identifier;
				foreach ($multipliers[$defendingTypeIdentifier] as $attackingTypeIdentifier => $multiplier) {
					$pokemonAbilityTypeMultipliers[$pokemonId]['none'][$attackingTypeIdentifier] *= $multiplier;
				}
			}

			if ($includeAbilityMatchups) {
				// Apply this Pokémon's abilities to its type matchups.
				foreach ($pokemon->abilities as $ability) {
					$hasMatchups = $this->abilityTypeMatchups->hasMatchups(
						$versionGroup->generationId,
						$ability->identifier,
					);

					if ($hasMatchups) {
						$abilityIdentifier = $ability->identifier;

						$abilityMultipliers = $this->abilityTypeMatchups->getMatchups(
							$versionGroup->generationId,
							$ability->identifier,
							$pokemonAbilityTypeMultipliers[$pokemonId]['none'],
						);

						$pokemonAbilityTypeMultipliers[$pokemonId][$abilityIdentifier] = $abilityMultipliers;
					}
				}
			}
		}

		// A Pokemon will be included in the final results if any of its
		// abilities match all conditions.
		$final = [];

		foreach ($pokemonAbilityTypeMultipliers as $pokemonId => $abilityTypeMultipliers) {
			foreach ($abilityTypeMultipliers as $typeMultipliers) {
				foreach ($matchups as $typeIdentifier => $comparisons) {
					foreach ($comparisons as $comparison) {
						$multiplier = $typeMultipliers[$typeIdentifier];
						if (!$comparison->evaluate($multiplier)) {
							// This ability doesn't work for this Pokémon.
							// Skip to this Pokémon's next ability.
							continue 3;
						}
					}
					// All conditions for this type are true.
				}
				// All conditions for this ability are true. Regardless of how
				// this Pokémon's other abilities might turn out, add this
				// Pokémon to the list.
				$final[] = $pokemons[$pokemonId];
				continue 2;
			}
		}

		return $final;
	}
}
