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
	 * Get the Pokémon's egg groups.
	 *
	 * @param int $versionGroupId
	 * @param int $pokemonId
	 *
	 * @return int[]
	 */
	public function getEggGroupIds(int $versionGroupId, int $pokemonId) : array;

	/**
	 * Get this Pokémon's evolution.
	 *
	 * @param int $pokemonId
	 * @param int $versionGroupId
	 *
	 * @return int Pokémon id.
	 */
	public function getEvolution(int $versionGroupId, int $pokemonId) : int;

	/**
	 * Get Pokémon that share at least one egg group with the current Pokemon,
	 * and are not in any of the previously traversed egg groups.
	 *
	 * @param int $versionGroupId
	 * @param int $pokemonId
	 * @param string $eggGroups An imploded int[] of egg group ids.
	 * @param string $excludeEggGroups An imploded int[] of egg group ids.
	 *
	 * @return array
	 */
	public function getInSameEggGroupIds(
		int $versionGroupId,
		int $pokemonId,
		string $eggGroups,
		string $excludeEggGroups,
	) : array;

	/**
	 * Get Pokémon that share at least one egg group with the current Pokémon,
	 * have at least one egg group not shared with the current Pokémon, and are
	 * not in any of the previously traversed egg groups.
	 *
	 * @param int $versionGroupId
	 * @param string $eggGroups An imploded int[] of egg group ids.
	 * @param string $excludeEggGroups An imploded int[] of egg group ids.
	 *
	 * @return array
	 */
	public function getInOtherEggGroupIds(
		int $versionGroupId,
		string $eggGroups,
		string $excludeEggGroups,
	) : array;

	/**
	 * Get Pokémon that learn this move by non-egg between gen 3 and the current
	 * generation, and have no other egg groups.
	 *
	 * @param int $versionGroupId
	 * @param int $moveId
	 * @param string $inSameEggGroup An imploded int[] of Pokémon ids.
	 *
	 * @return array
	 */
	public function getByNonEgg(
		int $versionGroupId,
		int $moveId,
		string $inSameEggGroup,
	) : array;

	/**
	 * Get Pokémon that learn this move by egg between gen 3 and the current
	 * generation, and have another egg group.
	 *
	 * @param int $versionGroupId
	 * @param int $moveId
	 * @param string $inOtherEggGroup An imploded int[] of Pokémon ids.
	 *
	 * @return array
	 */
	public function getByEgg(
		int $versionGroupId,
		int $moveId,
		string $inOtherEggGroup,
	) : array;
}
