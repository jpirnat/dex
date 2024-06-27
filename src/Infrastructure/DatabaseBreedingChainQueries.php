<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\BreedingChains\BreedingChainQueriesInterface;
use Jp\Dex\Domain\PokemonMoves\MoveMethodId;
use PDO;

final readonly class DatabaseBreedingChainQueries implements BreedingChainQueriesInterface
{
	public function __construct(
		private PDO $db,
	) {}

	/**
	 * Get female-only Pokémon introduced prior to gen 6.
	 *
	 * @return int[]
	 */
	public function getFemaleOnlyPokemon() : array
	{
		$stmt = $this->db->query(
			'SELECT
				`id`
			FROM `pokemon`
			WHERE `gender_ratio` = 8
			AND `id` IN (
				SELECT
					`form_id`
				FROM `version_group_forms`
				WHERE `version_group_id` IN (
					SELECT
						`id`
					FROM `version_groups`
					WHERE `generation_id` < 6
				)
			)'
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
			WHERE `id` = $versionGroupId
			LIMIT 1"
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
	) : array {
		$stmt = $this->db->query(
			"SELECT DISTINCT
				`pokemon_id`
			FROM `forms`
			WHERE `is_battle_only` = 0
			AND `pokemon_id` <> $pokemonId
			AND `pokemon_id` IN (
				# Make sure the Pokémon is in the same game as the egg move.
				SELECT
					`f`.`pokemon_id`
				FROM `version_group_forms` AS `vgf`
				INNER JOIN `forms` AS `f`
					ON `vgf`.`form_id` = `f`.`id`
				WHERE `vgf`.`version_group_id` = $versionGroupId
			)
			AND `pokemon_id` IN (
				SELECT
					`peg`.`pokemon_id`
				FROM `pokemon_egg_groups` AS `peg`
				INNER JOIN `version_groups` AS `vg`
					ON `peg`.`generation_id` = `vg`.`generation_id`
				WHERE `peg`.`egg_group_id` IN ($eggGroups)
					AND `vg`.`id` = $versionGroupId
			)
			AND `pokemon_id` NOT IN (
				SELECT
					`peg`.`pokemon_id`
				FROM `pokemon_egg_groups` AS `peg`
				INNER JOIN `version_groups` AS `vg`
					ON `peg`.`generation_id` = `vg`.`generation_id`
				WHERE `peg`.`egg_group_id` IN ($excludeEggGroups)
					AND `vg`.`id` = $versionGroupId
			)
			AND `pokemon_id` NOT IN (
				SELECT
					`id`
				FROM `pokemon`
				WHERE `gender_ratio` = -1
			)"
		);
		return $stmt->fetchAll(PDO::FETCH_COLUMN);
	}

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
	) : array {
		$stmt = $this->db->query(
			"SELECT DISTINCT
				`pokemon_id`
			FROM `forms`
			WHERE `is_battle_only` = 0
			AND `pokemon_id` IN (
				# Make sure the Pokémon is in the same game as the egg move.
				SELECT
					`f`.`pokemon_id`
				FROM `version_group_forms` AS `vgf`
				INNER JOIN `forms` AS `f`
					ON `vgf`.`form_id` = `f`.`id`
				WHERE `vgf`.`version_group_id` = $versionGroupId
			)
			AND `pokemon_id` IN (
				SELECT
					`peg`.`pokemon_id`
				FROM `pokemon_egg_groups` AS `peg`
				INNER JOIN `version_groups` AS `vg`
					ON `peg`.`generation_id` = `vg`.`generation_id`
				WHERE `peg`.`egg_group_id` IN ($eggGroups)
					AND `vg`.`id` = $versionGroupId
			)
			AND `pokemon_id` IN (
				SELECT
					`peg`.`pokemon_id`
				FROM `pokemon_egg_groups` AS `peg`
				INNER JOIN `version_groups` AS `vg`
					ON `peg`.`generation_id` = `vg`.`generation_id`
				WHERE `peg`.`egg_group_id` NOT IN ($eggGroups)
					AND `vg`.`id` = $versionGroupId
			)
			AND `pokemon_id` NOT IN (
				SELECT
					`peg`.`pokemon_id`
				FROM `pokemon_egg_groups` AS `peg`
				INNER JOIN `version_groups` AS `vg`
					ON `peg`.`generation_id` = `vg`.`generation_id`
				WHERE `peg`.`egg_group_id` IN ($excludeEggGroups)
					AND `vg`.`id` = $versionGroupId
			)
			AND `pokemon_id` NOT IN (
				SELECT
					`id`
				FROM `pokemon`
				WHERE `gender_ratio` = -1
			)"
		);
		return $stmt->fetchAll(PDO::FETCH_COLUMN);
	}

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
	) : array {
		$egg = MoveMethodId::EGG;

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
			WHERE `pm`.`version_group_id` IN (
				SELECT
					`from_vg_id`
				FROM `vg_move_transfers`
				WHERE `into_vg_id` = $versionGroupId
			)
				AND `move_id` = $moveId
				AND `pm`.`move_method_id` <> $egg
				AND `pm`.`pokemon_id` IN ($inSameEggGroup)
			ORDER BY
				`vg`.`sort` DESC,
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
	) : array {
		$egg = MoveMethodId::EGG;

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
			WHERE `pm`.`version_group_id` IN (
				SELECT
					`from_vg_id`
				FROM `vg_move_transfers`
				WHERE `into_vg_id` = $versionGroupId
			)
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
