<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Items\DexItem;
use Jp\Dex\Domain\Items\DexItemRepositoryInterface;
use Jp\Dex\Domain\Items\ItemId;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Versions\VersionGroupId;
use PDO;
use PDOStatement;

final readonly class DatabaseDexItemRepository implements DexItemRepositoryInterface
{
	public function __construct(
		private PDO $db,
	) {}

	private function getBaseQuery() : string
	{
		return
"SELECT
	`i`.`id`,
	`vi`.`icon`,
	`i`.`identifier`,
	COALESCE(`id`.`name`, `in`.`name`) AS `name`,
	`id`.`description`
FROM `vg_items` AS `vi`
INNER JOIN `items` AS `i`
	ON `vi`.`item_id` = `i`.`id`
INNER JOIN `item_names` AS `in`
	ON `vi`.`item_id` = `in`.`item_id`
LEFT JOIN `item_descriptions` AS `id`
	ON `vi`.`version_group_id` = `id`.`version_group_id`
	AND `vi`.`item_id` = `id`.`item_id`
	AND `in`.`language_id` = `id`.`language_id`
";
	}

	/**
	 * @return DexItem[] Indexed by id.
	 */
	private function executeAndFetch(PDOStatement $stmt) : array
	{
		$stmt->execute();

		$items = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$items[$result['id']] = $this->fromRecord($result);
		}

		return $items;
	}

	private function fromRecord(array $result) : DexItem
	{
		return new DexItem(
			$result['icon'],
			$result['identifier'],
			$result['name'],
			$result['description'] ?? '',
		);
	}

	/**
	 * Get a dex item by its id.
	 */
	public function getById(
		VersionGroupId $versionGroupId,
		ItemId $itemId,
		LanguageId $languageId,
	) : DexItem {
		$baseQuery = $this->getBaseQuery();
		$stmt = $this->db->prepare(
			"$baseQuery
			WHERE `vi`.`version_group_id` = :version_group_id
				AND `i`.`id` = :item_id
				AND `in`.`language_id` = :language_id
			LIMIT 1"
		);
		$stmt->bindValue(':version_group_id', $versionGroupId->value, PDO::PARAM_INT);
		$stmt->bindValue(':item_id', $itemId->value, PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value, PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		return $this->fromRecord($result);
	}

	/**
	 * Get all dex items in this version group.
	 *
	 * @return DexItem[] Indexed by id. Ordered by name.
	 */
	public function getByVersionGroup(
		VersionGroupId $versionGroupId,
		LanguageId $languageId,
	) : array {
		$baseQuery = $this->getBaseQuery();
		$stmt = $this->db->prepare(
			"$baseQuery
			WHERE `vi`.`version_group_id` = :version_group_id
				AND `vi`.`is_available` = 1
				AND `in`.`language_id` = :language_id
			ORDER BY `name`"
		);
		$stmt->bindValue(':version_group_id', $versionGroupId->value, PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value, PDO::PARAM_INT);
		return $this->executeAndFetch($stmt);
	}

	/**
	 * Get dex items for this version group's TMs.
	 *
	 * @return DexItem[] Indexed by id.
	 */
	public function getTmsByVg(
		VersionGroupId $versionGroupId,
		LanguageId $languageId,
	) : array {
		$baseQuery = $this->getBaseQuery();
		$stmt = $this->db->prepare(
			"$baseQuery
			WHERE `vi`.`version_group_id` = :version_group_id1
				AND `vi`.`item_id` IN (
					SELECT
						`item_id`
					FROM `technical_machines`
					WHERE `version_group_id` = :version_group_id2
				)
				AND `vi`.`is_available` = 1
				AND `in`.`language_id` = :language_id"
		);
		$stmt->bindValue(':version_group_id1', $versionGroupId->value, PDO::PARAM_INT);
		$stmt->bindValue(':version_group_id2', $versionGroupId->value, PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value, PDO::PARAM_INT);
		return $this->executeAndFetch($stmt);
	}
}
