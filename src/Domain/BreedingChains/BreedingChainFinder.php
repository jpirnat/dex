<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\BreedingChains;

use Jp\Dex\Domain\EggGroups\EggGroupId;
use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\PokemonMoves\MoveMethodId;
use Jp\Dex\Domain\PokemonMoves\PokemonMove;
use Jp\Dex\Domain\Versions\VersionGroupId;

final class BreedingChainFinder
{
	/** @var int[] $femaleOnlyPokemonIds */
	private array $femaleOnlyPokemonIds = [];


	public function __construct(
		private BreedingChainQueriesInterface $breedingChainQueries,
	) {}


	/**
	 * Find breeding chains that allow this Pokémon to learn this egg move.
	 *
	 * @return PokemonMove[][]
	 */
	public function findChains(
		PokemonId $pokemonId,
		MoveId $moveId,
		VersionGroupId $versionGroupId
	) : array {
		$pokemonId = $pokemonId->value();
		$moveId = $moveId->value();
		$versionGroupId = $versionGroupId->value();

		// Set female-only Pokémon introduced prior to gen 6.
		$this->femaleOnlyPokemonIds = $this->breedingChainQueries->getFemaleOnlyPokemon();

		$breedingTree = $this->realFindChains($pokemonId, $moveId, $versionGroupId, [0]);

		// Flatten the tree structure into arrays.
		$chains = $breedingTree->getChains();

		// Sort chains from shortest to longest.
		usort($chains, function (array $a, array $b) : int {
		  return count($a) <=> count($b);
		});

		// Limit to 50 results.
		// $chains = array_slice($chains, 0, 50);

		return $chains;
	}

	/**
	 * Find breeding chains that allow this Pokémon to learn this egg move.
	 *
	 * @param int[] $excludeEggGroupIds
	 */
	private function realFindChains(
		int $pokemonId,
		int $moveId,
		int $versionGroupId,
		array $excludeEggGroupIds
	) : BreedingTree {
		$breedingTree = new BreedingTree(new PokemonMove(
			new PokemonId($pokemonId),
			new VersionGroupId($versionGroupId),
			new MoveId($moveId),
			new MoveMethodId(MoveMethodId::EGG),
			0,
			0
		));

		$generationId = $this->breedingChainQueries->getGenerationId($versionGroupId);

		// Get the Pokémon's egg groups.
		$eggGroupIds = $this->breedingChainQueries->getEggGroupIds($pokemonId, $generationId);
		// In future recursions from this node, these egg groups need to be
		// added to $excludeEggGroupIds, so we don't get stuck in a cycle.

		// If this is a baby Pokémon, use the egg groups of its evolution.
		if ($eggGroupIds === [EggGroupId::UNDISCOVERED]) {
			$evolvedPokemonId = $this->breedingChainQueries->getEvolution(
				$pokemonId,
				$versionGroupId
			);
			if ($evolvedPokemonId) {
				$eggGroupIds = $this->breedingChainQueries->getEggGroupIds(
					$evolvedPokemonId,
					$generationId
				);
			}
		}

		$eggGroups = implode(', ', $eggGroupIds);
		$excludeEggGroups = implode(', ', $excludeEggGroupIds);

		// Get Pokémon that share at least one egg group with the current
		// Pokémon, and are not in any of the previously traversed egg groups.
		$inSameEggGroupIds = $this->breedingChainQueries->getInSameEggGroupIds(
			$pokemonId,
			$generationId,
			$eggGroups,
			$excludeEggGroups
		);

		// Get Pokémon that share at least one egg group with the current
		// Pokémon, have at least one egg group not shared with the current
		// Pokémon, and are not in any of the previously traversed egg groups.
		$inOtherEggGroupIds = $this->breedingChainQueries->getInOtherEggGroupIds(
			$generationId,
			$eggGroups,
			$excludeEggGroups
		);

		// BUG FIX - If there are no Pokémon in other egg groups, initialize the
		// array anyway just so the query later on doesn't break.
		if ($inOtherEggGroupIds === []) {
			$inOtherEggGroupIds = [0];
		}

		$inSameEggGroup = implode(', ', $inSameEggGroupIds);
		$inOtherEggGroup = implode(', ', $inOtherEggGroupIds);

		// Get other Pokémon that learn this move by non-egg between gen 3 and
		// the current generation, and have no other egg groups.
		$pokemonMoves = $this->breedingChainQueries->getByNonEgg(
			$generationId,
			$moveId,
			$inSameEggGroup
		);

		foreach ($pokemonMoves as $pokemonMove) {
			// If this Pokémon is already in the set of potential parents, skip
			// it. It only needs to be added once.
			if ($breedingTree->hasParent($pokemonMove['pokemonId'])) {
				continue;
			}

			// Before gen 6, only male Pokémon could pass down egg moves.
			if ($pokemonMove['generationId'] < 6
				&& in_array($pokemonMove['pokemonId'], $this->femaleOnlyPokemonIds)
			) {
				continue;
			}

			// Add this potential parent to the breeding tree.
			$breedingTree->addParent(new BreedingTree(new PokemonMove(
				new PokemonId($pokemonMove['pokemonId']),
				new VersionGroupId($pokemonMove['versionGroupId']),
				new MoveId($moveId),
				new MoveMethodId($pokemonMove['moveMethodId']),
				$pokemonMove['level'],
				$pokemonMove['sort']
			)));
		}

		/*
		// If parents that learn the move naturally have already been found, we
		// don't need to build an extended chain. We can quit right here.
		if ($breedingTree->getParents() !== []) {
			return $breedingTree;
		}
		*/

		// Get other Pokémon that learn this move by egg between gen 3 and the
		// current generation, and have another egg group.
		$pokemonMoves = $this->breedingChainQueries->getByEgg(
			$generationId,
			$moveId,
			$inOtherEggGroup
		);

		foreach ($pokemonMoves as $pokemonMove) {
			// If this Pokémon is already in the set of potential parents, skip
			// it. It only needs to be added once.
			if ($breedingTree->hasParent($pokemonMove['pokemonId'])) {
				continue;
			}

			// Before gen 6, only male Pokémon could pass down egg moves.
			if ($pokemonMove['generationId'] < 6
				&& in_array($pokemonMove['pokemonId'], $this->femaleOnlyPokemonIds)
			) {
				continue;
			}

			// This Pokémon learns the move by egg. Recursively find its own
			// breeding chains.
			$extendedTree = $this->realFindChains(
				$pokemonMove['pokemonId'],
				$moveId,
				$pokemonMove['versionGroupId'],
				array_merge($excludeEggGroupIds, $eggGroupIds)
			);

			if ($extendedTree->isComplete()) {
				$breedingTree->addParent($extendedTree);
			}
		}

		return $breedingTree;
	}
}
