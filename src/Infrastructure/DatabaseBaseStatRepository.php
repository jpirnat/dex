<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Abilities\AbilityId;
use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Stats\BaseStatRepositoryInterface;
use Jp\Dex\Domain\Types\TypeId;
use Jp\Dex\Domain\Versions\VersionGroupId;
use PDO;

final readonly class DatabaseBaseStatRepository implements BaseStatRepositoryInterface
{
	public function __construct(
		private PDO $db,
	) {}

	/**
	 * Get a Pokémon's base stats by version group and Pokémon.
	 *
	 * @return int[] Indexed by stat identifier.
	 */
	public function getByPokemon(
		VersionGroupId $versionGroupId,
		PokemonId $pokemonId,
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`s`.`identifier`,
				`bs`.`value`
			FROM `base_stats` AS `bs`
			INNER JOIN `stats` AS `s`
				ON `bs`.`stat_id` = `s`.`id`
			WHERE `bs`.`version_group_id` = :version_group_id
				AND `bs`.`pokemon_id` = :pokemon_id
			ORDER BY
				`bs`.`pokemon_id`,
				`s`.`sort`'
		);
		$stmt->bindValue(':version_group_id', $versionGroupId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $pokemonId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$baseStats = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$baseStats[$result['identifier']] = $result['value'];
		}

		return $baseStats;
	}

	/**
	 * Get all base stats had by Pokémon with this ability.
	 * This method is used to get data for the dex ability page.
	 *
	 * @return int[][] Indexed first by Pokémon id, then by stat identifier.
	 */
	public function getByPokemonAbility(
		VersionGroupId $versionGroupId,
		AbilityId $abilityId,
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`bs`.`pokemon_id`,
				`s`.`identifier`,
				`bs`.`value`
			FROM `base_stats` AS `bs`
			INNER JOIN `stats` AS `s`
				ON `bs`.`stat_id` = `s`.`id`
			WHERE `bs`.`version_group_id` = :version_group_id1
				AND `bs`.`pokemon_id` IN (
					SELECT
						`pokemon_id`
					FROM `pokemon_abilities`
					WHERE `version_group_id` = :version_group_id2
						AND `ability_id` = :ability_id
				)
			ORDER BY
				`bs`.`pokemon_id`,
				`s`.`sort`'
		);
		$stmt->bindValue(':version_group_id1', $versionGroupId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':version_group_id2', $versionGroupId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':ability_id', $abilityId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$baseStats = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$baseStats[$result['pokemon_id']][$result['identifier']] = $result['value'];
		}

		return $baseStats;
	}

	/**
	 * Get all base stats had by Pokémon with this move.
	 * This method is used to get data for the dex move page.
	 *
	 * @return int[][] Indexed first by Pokémon id, then by stat identifier.
	 */
	public function getByPokemonMove(
		VersionGroupId $versionGroupId,
		MoveId $moveId,
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`bs`.`pokemon_id`,
				`s`.`identifier`,
				`bs`.`value`
			FROM `base_stats` AS `bs`
			INNER JOIN `stats` AS `s`
				ON `bs`.`stat_id` = `s`.`id`
			WHERE `bs`.`version_group_id` = :version_group_id1
				AND `bs`.`pokemon_id` IN (
					SELECT
						`pokemon_id`
					FROM `pokemon_moves`
					WHERE `version_group_id` IN (
						SELECT
							`from_vg_id`
						FROM `vg_move_transfers`
						WHERE `into_vg_id` = :version_group_id2
					)
					AND `move_id` = :move_id
				)
			ORDER BY
				`bs`.`pokemon_id`,
				`s`.`sort`'
		);
		$stmt->bindValue(':version_group_id1', $versionGroupId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':version_group_id2', $versionGroupId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':move_id', $moveId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$baseStats = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$baseStats[$result['pokemon_id']][$result['identifier']] = $result['value'];
		}

		return $baseStats;
	}

	/**
	 * Get all base stats had by Pokémon in this version group.
	 * This method is used to get data for the dex Pokémons page.
	 *
	 * @return int[][] Indexed first by Pokémon id, then by stat identifier.
	 */
	public function getByVersionGroup(VersionGroupId $versionGroupId) : array
	{
		$stmt = $this->db->prepare(
			'SELECT
				`bs`.`pokemon_id`,
				`s`.`identifier`,
				`bs`.`value`
			FROM `base_stats` AS `bs`
			INNER JOIN `stats` AS `s`
				ON `bs`.`stat_id` = `s`.`id`
			WHERE `bs`.`version_group_id` = :version_group_id
			ORDER BY
				`bs`.`pokemon_id`,
				`s`.`sort`'
		);
		$stmt->bindValue(':version_group_id', $versionGroupId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$baseStats = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$baseStats[$result['pokemon_id']][$result['identifier']] = $result['value'];
		}

		return $baseStats;
	}

	/**
	 * Get all base stats had by Pokémon with this type.
	 * This method is used to get data for the dex type page.
	 *
	 * @return int[][] Indexed first by Pokémon id, then by stat identifier.
	 */
	public function getByPokemonType(
		VersionGroupId $versionGroupId,
		TypeId $typeId,
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`bs`.`pokemon_id`,
				`s`.`identifier`,
				`bs`.`value`
			FROM `base_stats` AS `bs`
			INNER JOIN `stats` AS `s`
				ON `bs`.`stat_id` = `s`.`id`
			WHERE `bs`.`version_group_id` = :version_group_id1
				AND `bs`.`pokemon_id` IN (
					SELECT
						`pokemon_id`
					FROM `pokemon_types`
					WHERE `version_group_id` = :version_group_id2
						AND `type_id` = :type_id
				)'
		);
		$stmt->bindValue(':version_group_id1', $versionGroupId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':version_group_id2', $versionGroupId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':type_id', $typeId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$baseStats = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$baseStats[$result['pokemon_id']][$result['identifier']] = $result['value'];
		}

		return $baseStats;
	}
}
