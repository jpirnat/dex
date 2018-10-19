<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\BreedingChains\BreedingChainQueriesInterface;
use Jp\Dex\Domain\PokemonMoves\MoveMethodId;
use PDO;

class DatabaseBreedingChainQueries implements BreedingChainQueriesInterface
{
	/** @var PDO $db */
	private $db;

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
				AND `vg`.`generation` < 6'
		);
		return $stmt->fetchAll(PDO::FETCH_COLUMN);
	}

	/**
	 * Get the Pokémon's egg groups.
	 *
	 * @param int $pokemonId
	 *
	 * @return int[]
	 */
	public function getEggGroupIds(int $pokemonId) : array
	{
		$stmt = $this->db->query(
			"SELECT
				`egg_group_id`
			FROM `pokemon_egg_groups`
			WHERE `pokemon_id` = $pokemonId"
		);
		return $stmt->fetchAll(PDO::FETCH_COLUMN);
	}

	/**
	 * Get Pokémon that share at least one egg group with the current Pokemon,
	 * and are not in any of the previously traversed egg groups.
	 *
	 * @param int $pokemonId
	 * @param string $eggGroups An imploded int[] of egg group ids.
	 * @param string $excludeEggGroups An imploded int[] of egg group ids.
	 *
	 * @return array
	 */
	public function getInSameEggGroupIds(
		int $pokemonId,
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
			)
			AND `pokemon_id` NOT IN
			(
				SELECT
					`pokemon_id`
				FROM `pokemon_egg_groups`
				WHERE `egg_group_id` IN ($excludeEggGroups)
			)"
		);
		return $stmt->fetchAll(PDO::FETCH_COLUMN);
	}

	/**
	 * Get Pokémon that share at least one egg group with the current Pokémon,
	 * have at least one egg group not shared with the current Pokémon, and are
	 * not in any of the previously traversed egg groups.
	 *
	 * @param string $eggGroups An imploded int[] of egg group ids.
	 * @param string $excludeEggGroups An imploded int[] of egg group ids.
	 *
	 * @return array
	 */
	public function getInOtherEggGroupIds(
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
			)
			AND `pokemon_id` IN
			(
				SELECT
					`pokemon_id`
				FROM `pokemon_egg_groups`
				WHERE `egg_group_id` NOT IN ($eggGroups)
			)
			AND `pokemon_id` NOT IN
			(
				SELECT
					`pokemon_id`
				FROM `pokemon_egg_groups`
				WHERE `egg_group_id` IN ($excludeEggGroups)
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
		string $inSameEggGroup
	) : array {
		$egg = MoveMethodId::EGG;
		$stmt = $this->db->query(
			"SELECT
				`pm`.`pokemon_id` AS `pokemonId`,
				`pm`.`version_group_id` AS `versionGroupId`,
				`pm`.`move_method_id` AS `moveMethodId`,
				`pm`.`level`,
				`pm`.`sort`,
				`vg`.`generation`
			FROM `pokemon_moves` AS `pm`
			INNER JOIN `version_groups` AS `vg`
				ON `pm`.`version_group_id` = `vg`.`id`
			WHERE `vg`.`generation` BETWEEN 3 AND
				(
					SELECT
						`generation`
					FROM `version_groups`
					WHERE `id` = $versionGroupId
					LIMIT 1
				)
				AND `move_id` = $moveId
				AND `pm`.`move_method_id` <> $egg
				AND `pm`.`pokemon_id` IN ($inSameEggGroup)
			ORDER BY
				`generation` DESC,
				`version_group_id` DESC"
		);
		// Prioritize newer generations, and newer versions within those
		// generations. TODO: This will be a problem when Colosseum/XD are
		// prioritized over the main games.
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
		string $inOtherEggGroup
	) : array {
		$egg = MoveMethodId::EGG;
		$stmt = $this->db->query(
			"SELECT
				`pm`.`pokemon_id` AS `pokemonId`,
				`pm`.`version_group_id` AS `versionGroupId`,
				`pm`.`move_method_id` AS `moveMethodId`,
				`pm`.`level`,
				`pm`.`sort`,
				`vg`.`generation`
			FROM `pokemon_moves` AS `pm`
			INNER JOIN `version_groups` AS `vg`
				ON `pm`.`version_group_id` = `vg`.`id`
			WHERE `vg`.`generation` BETWEEN 3 AND
				(
					SELECT
						`generation`
					FROM `version_groups`
					WHERE `id` = $versionGroupId
					LIMIT 1
				)
				AND `move_id` = $moveId
				AND `pm`.`move_method_id` = $egg
				AND `pm`.`pokemon_id` IN ($inOtherEggGroup)
			ORDER BY
				`generation` DESC,
				`version_group_id` DESC"
		);
		// Prioritize newer generations, and newer versions within those
		// generations. TODO: This will be a problem when Colosseum/XD are
		// prioritized over the main games.
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
}
