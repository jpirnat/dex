<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\BreedingChains;

interface BreedingChainQueriesInterface
{
	/**
	 * Get female-only Pokémon introduced prior to gen 6.
	 *
	 * @return int[]
	 */
	public function getFemaleOnlyPokemon() : array;

	/**
	 * Get the version group's generation.
	 *
	 * @param int $versionGroupId
	 *
	 * @return int Generation id
	 */
	public function getGenerationId(int $versionGroupId) : int;

	/**
	 * Get the Pokémon's egg groups.
	 *
	 * @param int $pokemonId
	 * @param int $generationId
	 *
	 * @return int[]
	 */
	public function getEggGroupIds(int $pokemonId, int $generationId) : array;

	/**
	 * Get this Pokémon's evolution.
	 *
	 * @param int $pokemonId
	 * @param int $versionGroupId
	 *
	 * @return int Pokémon id.
	 */
	public function getEvolution(int $pokemonId, int $versionGroupId) : int;

	/**
	 * Get Pokémon that share at least one egg group with the current Pokemon,
	 * and are not in any of the previously traversed egg groups.
	 *
	 * @param int $pokemonId
	 * @param int $generationId
	 * @param string $eggGroups An imploded int[] of egg group ids.
	 * @param string $excludeEggGroups An imploded int[] of egg group ids.
	 *
	 * @return array
	 */
	public function getInSameEggGroupIds(
		int $pokemonId,
		int $generationId,
		string $eggGroups,
		string $excludeEggGroups
	) : array;

	/**
	 * Get Pokémon that share at least one egg group with the current Pokémon,
	 * have at least one egg group not shared with the current Pokémon, and are
	 * not in any of the previously traversed egg groups.
	 *
	 * @param int $generationId
	 * @param string $eggGroups An imploded int[] of egg group ids.
	 * @param string $excludeEggGroups An imploded int[] of egg group ids.
	 *
	 * @return array
	 */
	public function getInOtherEggGroupIds(
		int $generationId,
		string $eggGroups,
		string $excludeEggGroups
	) : array;

	/**
	 * Get Pokémon that learn this move by non-egg between gen 3 and the current
	 * generation, and have no other egg groups.
	 *
	 * @param int $generationId
	 * @param int $moveId
	 * @param string $inSameEggGroup An imploded int[] of Pokémon ids.
	 *
	 * @return array
	 */
	public function getByNonEgg(
		int $generationId,
		int $moveId,
		string $inSameEggGroup
	) : array;

	/**
	 * Get Pokémon that learn this move by egg between gen 3 and the current
	 * generation, and have another egg group.
	 *
	 * @param int $generationId
	 * @param int $moveId
	 * @param string $inOtherEggGroup An imploded int[] of Pokémon ids.
	 *
	 * @return array
	 */
	public function getByEgg(
		int $generationId,
		int $moveId,
		string $inOtherEggGroup
	) : array;
}
