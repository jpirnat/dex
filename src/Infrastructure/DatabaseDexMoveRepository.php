<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Categories\DexCategory;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Moves\DexMove;
use Jp\Dex\Domain\Moves\DexMoveRepositoryInterface;
use Jp\Dex\Domain\Moves\Flags\MoveFlagId;
use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Types\DexType;
use Jp\Dex\Domain\Types\TypeId;
use Jp\Dex\Domain\Versions\VersionGroupId;
use PDO;
use PDOStatement;

final readonly class DatabaseDexMoveRepository implements DexMoveRepositoryInterface
{
	public function __construct(
		private PDO $db,
	) {}

	public function getBaseQuery() : string
	{
		return
"SELECT
	`m`.`id`,
	`m`.`identifier`,
	COALESCE(`md`.`name`, `mn`.`name`) AS `name`,
	`vm`.`type_id`,
	`vm`.`category_id`,

	`t`.`identifier` AS `type_identifier`,
	`tn`.`name` AS `type_name`,
	`ti`.`icon` AS `type_icon`,

	`c`.`identifier` AS `category_identifier`,
	`c`.`icon` AS `category_icon`,
	`cn`.`name` AS `category_name`,

	`vm`.`pp`,
	`vm`.`power`,
	`vm`.`accuracy`,
	`md`.`description`
FROM `vg_moves` AS `vm`
INNER JOIN `moves` AS `m`
	ON `vm`.`move_id` = `m`.`id`
INNER JOIN `move_names` AS `mn`
	ON `m`.`id` = `mn`.`move_id`
LEFT JOIN `move_descriptions` AS `md`
	ON `vm`.`version_group_id` = `md`.`version_group_id`
	AND `vm`.`move_id` = `md`.`move_id`
	AND `mn`.`language_id` = `md`.`language_id`

INNER JOIN `types` AS `t`
	ON `vm`.`type_id` = `t`.`id`
INNER JOIN `type_names` AS `tn`
	ON `mn`.`language_id` = `tn`.`language_id`
	AND `vm`.`type_id` = `tn`.`type_id`
LEFT JOIN `type_icons` AS `ti`
	ON `mn`.`language_id` = `ti`.`language_id`
	AND `vm`.`type_id` = `ti`.`type_id`

INNER JOIN `categories` AS `c`
	ON `vm`.`category_id` = `c`.`id`
INNER JOIN `category_names` AS `cn`
	ON `vm`.`category_id` = `cn`.`category_id`
	AND `mn`.`language_id` = `cn`.`language_id`
";
	}

	/**
	 * @return DexMove[] Indexed by id.
	 */
	public function executeAndFetch(PDOStatement $stmt) : array
	{
		$stmt->execute();

		$moves = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$moves[$result['id']] = $this->fromRecord($result);
		}

		return $moves;
	}

	private function fromRecord(array $result) : DexMove
	{
		return new DexMove(
			$result['identifier'],
			$result['name'],
			new DexType(
				$result['type_identifier'],
				$result['type_name'],
				$result['type_icon'] ?? '',
			),
			new DexCategory(
				$result['category_identifier'],
				$result['category_icon'],
				$result['category_name'],
			),
			$result['pp'],
			$result['power'],
			$result['accuracy'],
			(string) $result['description'],
		);
	}

	/**
	 * Get a dex move by its id.
	 * This method is used to get data for the dex move page.
	 */
	public function getById(
		VersionGroupId $versionGroupId,
		MoveId $moveId,
		LanguageId $languageId,
	) : DexMove {
		$baseQuery = $this->getBaseQuery();
		$stmt = $this->db->prepare(
			"$baseQuery
			WHERE `vm`.`version_group_id` = :version_group_id
				AND `vm`.`move_id` = :move_id
				AND `mn`.`language_id` = :language_id
			LIMIT 1"
		);
		$stmt->bindValue(':version_group_id', $versionGroupId->value, PDO::PARAM_INT);
		$stmt->bindValue(':move_id', $moveId->value, PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value, PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		return $this->fromRecord($result);
	}

	/**
	 * Get all dex moves in this version group.
	 * This method is used to get data for the dex moves page.
	 *
	 * @return DexMove[] Indexed by move id. Ordered by name.
	 */
	public function getByVersionGroup(
		VersionGroupId $versionGroupId,
		LanguageId $languageId,
	) : array {
		$baseQuery = $this->getBaseQuery();
		$stmt = $this->db->prepare(
			"$baseQuery
			WHERE `vm`.`version_group_id` = :version_group_id
				AND `vm`.`can_use_move` = 1
				AND `mn`.`language_id` = :language_id
			ORDER BY `name`"
		);
		$stmt->bindValue(':version_group_id', $versionGroupId->value, PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value, PDO::PARAM_INT);
		return $this->executeAndFetch($stmt);
	}

	/**
	 * Get all dex moves in this version group.
	 * This method is used to get data for the dex moves page.
	 *
	 * @return DexMove[] Indexed by move id. Ordered by name.
	 */
	public function getByVgAndFlag(
		VersionGroupId $versionGroupId,
		MoveFlagId $flagId,
		LanguageId $languageId,
	) : array {
		$baseQuery = $this->getBaseQuery();
		$stmt = $this->db->prepare(
			"$baseQuery
			WHERE `vm`.`version_group_id` = :version_group_id1
				AND `vm`.`move_id` IN (
					SELECT
						`move_id`
					FROM `vg_moves_flags`
					WHERE `version_group_id` = :version_group_id2
						AND `flag_id` = :flag_id
				)
				AND `vm`.`can_use_move` = 1
				AND `mn`.`language_id` = :language_id
			ORDER BY `name`"
		);
		$stmt->bindValue(':version_group_id1', $versionGroupId->value, PDO::PARAM_INT);
		$stmt->bindValue(':version_group_id2', $versionGroupId->value, PDO::PARAM_INT);
		$stmt->bindValue(':flag_id', $flagId->value, PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value, PDO::PARAM_INT);
		return $this->executeAndFetch($stmt);
	}

	/**
	 * Get all dex moves learned by this Pokémon.
	 * This method is used to get data for the dex Pokémon page.
	 *
	 * @return DexMove[] Indexed by move id.
	 */
	public function getByPokemon(
		VersionGroupId $versionGroupId,
		PokemonId $pokemonId,
		LanguageId $languageId,
	) : array {
		$baseQuery = $this->getBaseQuery();
		$stmt = $this->db->prepare(
			"$baseQuery
			WHERE `vm`.`version_group_id` = :version_group_id1
				AND `vm`.`move_id` IN (
					SELECT
						`move_id`
					FROM `pokemon_moves`
					WHERE `version_group_id` IN (
						SELECT
							`from_vg_id`
						FROM `vg_move_transfers`
						WHERE `into_vg_id` = :version_group_id2
					)
					AND `pokemon_id` = :pokemon_id
				)
				AND `vm`.`can_use_move` = 1
				AND `mn`.`language_id` = :language_id"
		);
		$stmt->bindValue(':version_group_id1', $versionGroupId->value, PDO::PARAM_INT);
		$stmt->bindValue(':version_group_id2', $versionGroupId->value, PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $pokemonId->value, PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value, PDO::PARAM_INT);
		return $this->executeAndFetch($stmt);
	}

	/**
	 * Get all dex moves of this type.
	 * This method is used to get data for the dex type page.
	 *
	 * @return DexMove[] Indexed by move id. Ordered by name.
	 */
	public function getByType(
		VersionGroupId $versionGroupId,
		TypeId $typeId,
		LanguageId $languageId,
	) : array {
		$baseQuery = $this->getBaseQuery();
		$stmt = $this->db->prepare(
			"$baseQuery
			WHERE `vm`.`version_group_id` = :version_group_id
				AND `vm`.`can_use_move` = 1
				AND `vm`.`type_id` = :type_id
				AND `mn`.`language_id` = :language_id
			ORDER BY `name`"
		);
		$stmt->bindValue(':version_group_id', $versionGroupId->value, PDO::PARAM_INT);
		$stmt->bindValue(':type_id', $typeId->value, PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value, PDO::PARAM_INT);
		return $this->executeAndFetch($stmt);
	}

	/**
	 * Get dex moves for this version group's TMs.
	 *
	 * @return DexMove[] Indexed by move id.
	 */
	public function getTmsByVg(
		VersionGroupId $versionGroupId,
		LanguageId $languageId,
	) : array {
		$baseQuery = $this->getBaseQuery();
		$stmt = $this->db->prepare(
			"$baseQuery
			WHERE `vm`.`version_group_id` = :version_group_id1
				AND `vm`.`move_id` IN (
					SELECT
						`move_id`
					FROM `technical_machines`
					WHERE `version_group_id` = :version_group_id2
				)
				AND `vm`.`can_use_move` = 1
				AND `mn`.`language_id` = :language_id"
		);
		$stmt->bindValue(':version_group_id1', $versionGroupId->value, PDO::PARAM_INT);
		$stmt->bindValue(':version_group_id2', $versionGroupId->value, PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value, PDO::PARAM_INT);
		return $this->executeAndFetch($stmt);
	}
}
