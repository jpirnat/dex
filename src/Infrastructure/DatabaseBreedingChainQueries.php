<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\BreedingChains\BreedingChainQueriesInterface;
use Jp\Dex\Domain\PokemonMoves\MoveMethodId;
use PDO;

final class DatabaseBreedingChainQueries implements BreedingChainQueriesInterface
{
	private PDO $db;

	/**
	 * Constructor.
	 *
	 * @param PDO $db
	 */
	public function __construct(PDO $db)
	{
		$this->db = $db;
	}

	/**
	 * Get female-only Pokémon introduced prior to gen 6.
	 *
	 * @return int[]
	 */
	public function getFemaleOnlyPokemon() : array
	{
		$stmt = $this->db->query(
			'SELECT
				`p`.`id`
			FROM `pokemon` AS `p`
			INNER JOIN `version_groups` AS `vg`
				ON `p`.`introduced_in_version_group_id` = `vg`.`id`
			WHERE `p`.`gender_ratio` = 100
				AND `vg`.`generation_id` < 6'
		);
		return $stmt->fetchAll(PDO::FETCH_COLUMN);
	}

	/**
	 * Get the version group's generation.
	 *
	 * @param int $versionGroupId
	 *
	 * @return int Generation id
	 */
	public function getGenerationId(int $versionGroupId) : int
	{
		$stmt = $this->db->query(
			"SELECT
				`generation_id`
			FROM `version_groups`
			WHERE `id` = $versionGroupId"
		);
		return $stmt->fetchColumn();
	}

	/**
	 * Get the Pokémon's egg groups.
	 *
	 * @param int $pokemonId
	 * @param int $generationId
	 *
	 * @return int[]
	 */
	public function getEggGroupIds(int $pokemonId, int $generationId) : array
	{
		$stmt = $this->db->query(
			"SELECT
				`egg_group_id`
			FROM `pokemon_egg_groups`
			WHERE `pokemon_id` = $pokemonId
				AND `generation_id` = $generationId"
		);
		return $stmt->fetchAll(PDO::FETCH_COLUMN);
	}

	/**
	 * Get this Pokémon's evolution.
	 *
	 * @param int $pokemonId
	 * @param int $versionGroupId
	 *
	 * @return int Pokémon id.
	 */
	public function getEvolution(int $pokemonId, int $versionGroupId) : int
	{
		$stmt = $this->db->query(
			"SELECT
				`evo_into_id`
			FROM `evolutions`
			WHERE `version_group_id` = $versionGroupId
				AND `evo_from_id` = $pokemonId
			LIMIT 1"
		);
		return $stmt->fetchColumn();
	}

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
	) : array {
		$stmt = $this->db->query(
			"SELECT DISTINCT
				`pokemon_id`
			FROM `forms`
			WHERE `is_battle_only` = 0
			AND `pokemon_id` <> $pokemonId
			AND `pokemon_id` IN
			(
				SELECT
					`pokemon_id`
				FROM `pokemon_egg_groups`
				WHERE `egg_group_id` IN ($eggGroups)
					AND `generation_id` = $generationId
			)
			AND `pokemon_id` NOT IN
			(
				SELECT
					`pokemon_id`
				FROM `pokemon_egg_groups`
				WHERE `egg_group_id` IN ($excludeEggGroups)
					AND `generation_id` = $generationId
			)"
		);
		return $stmt->fetchAll(PDO::FETCH_COLUMN);
	}

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
	) : array {
		$stmt = $this->db->query(
			"SELECT DISTINCT
				`pokemon_id`
			FROM `forms`
			WHERE `is_battle_only` = 0
			AND `pokemon_id` IN
			(
				SELECT
					`pokemon_id`
				FROM `pokemon_egg_groups`
				WHERE `egg_group_id` IN ($eggGroups)
					AND `generation_id` = $generationId
			)
			AND `pokemon_id` IN
			(
				SELECT
					`pokemon_id`
				FROM `pokemon_egg_groups`
				WHERE `egg_group_id` NOT IN ($eggGroups)
					AND `generation_id` = $generationId
			)
			AND `pokemon_id` NOT IN
			(
				SELECT
					`pokemon_id`
				FROM `pokemon_egg_groups`
				WHERE `egg_group_id` IN ($excludeEggGroups)
					AND `generation_id` = $generationId
			)"
		);
		return $stmt->fetchAll(PDO::FETCH_COLUMN);
	}

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
	) : array {
		$egg = MoveMethodId::EGG;

		// If we're in gen 2, search only gen 2. If we're beyond gen 2, search
		// as far back as gen 3.
		$startGenerationId = $generationId === 2
			? 2
			: 3;

		$stmt = $this->db->query(
			"SELECT
				`pm`.`pokemon_id` AS `pokemonId`,
				`pm`.`version_group_id` AS `versionGroupId`,
				`pm`.`move_method_id` AS `moveMethodId`,
				`pm`.`level`,
				`pm`.`sort`,
				`vg`.`generation_id` AS `generationId`
			FROM `pokemon_moves` AS `pm`
			INNER JOIN `version_groups` AS `vg`
				ON `pm`.`version_group_id` = `vg`.`id`
			WHERE `vg`.`generation_id` BETWEEN $startGenerationId AND $generationId
				AND `move_id` = $moveId
				AND `pm`.`move_method_id` <> $egg
				AND `pm`.`pokemon_id` IN ($inSameEggGroup)
			ORDER BY
				`generation_id` DESC,
				`vg`.`breeding_priority`"
		);
		// Prioritize newer generations, and newer versions within those
		// generations.
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

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
	) : array {
		$egg = MoveMethodId::EGG;

		// If we're in gen 2, search only gen 2. If we're beyond gen 2, search
		// as far back as gen 3.
		$startGenerationId = $generationId === 2
			? 2
			: 3;

		$stmt = $this->db->query(
			"SELECT
				`pm`.`pokemon_id` AS `pokemonId`,
				`pm`.`version_group_id` AS `versionGroupId`,
				`pm`.`move_method_id` AS `moveMethodId`,
				`pm`.`level`,
				`pm`.`sort`,
				`vg`.`generation_id` AS `generationId`
			FROM `pokemon_moves` AS `pm`
			INNER JOIN `version_groups` AS `vg`
				ON `pm`.`version_group_id` = `vg`.`id`
			WHERE `vg`.`generation_id` BETWEEN $startGenerationId AND $generationId
				AND `move_id` = $moveId
				AND `pm`.`move_method_id` = $egg
				AND `pm`.`pokemon_id` IN ($inOtherEggGroup)
			ORDER BY
				`generation_id` DESC,
				`vg`.`breeding_priority`"
		);
		// Prioritize newer generations, and newer versions within those
		// generations.
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
}
