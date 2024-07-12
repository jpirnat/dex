<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Items\DexItem;
use Jp\Dex\Domain\Items\DexItemRepositoryInterface;
use Jp\Dex\Domain\Items\ItemId;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Versions\VersionGroupId;
use PDO;

final readonly class DatabaseDexItemRepository implements DexItemRepositoryInterface
{
	public function __construct(
		private PDO $db,
	) {}

	/**
	 * Get a dex item by its id.
	 */
	public function getById(
		VersionGroupId $versionGroupId,
		ItemId $itemId,
		LanguageId $languageId,
	) : DexItem {
		$stmt = $this->db->prepare(
			'SELECT
				`vgi`.`icon`,
				`i`.`identifier`,
				COALESCE(`id`.`name`, `in`.`name`) AS `name`,
				`id`.`description`
			FROM `items` AS `i`
			INNER JOIN `item_names` AS `in`
				ON `i`.`id` = `in`.`item_id`
			INNER JOIN `vg_items` AS `vgi`
				ON `i`.`id` = `vgi`.`item_id`
			LEFT JOIN `item_descriptions` AS `id`
				ON `vgi`.`version_group_id` = `id`.`version_group_id`
				AND `in`.`language_id` = `id`.`language_id`
				AND `i`.`id` = `id`.`item_id`
			WHERE `vgi`.`version_group_id` = :version_group_id
				AND `i`.`id` = :item_id
				AND `in`.`language_id` = :language_id
			LIMIT 1'
		);
		$stmt->bindValue(':version_group_id', $versionGroupId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':item_id', $itemId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		return new DexItem(
			$result['icon'],
			$result['identifier'],
			$result['name'],
			$result['description'] ?? '',
		);
	}

	/**
	 * Get all dex items in this version group.
	 *
	 * @return DexItem[] Ordered by name.
	 */
	public function getByVersionGroup(
		VersionGroupId $versionGroupId,
		LanguageId $languageId,
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`vgi`.`icon`,
				`i`.`identifier`,
				COALESCE(`id`.`name`, `in`.`name`) AS `name`,
				`id`.`description`
			FROM `items` AS `i`
			INNER JOIN `item_names` AS `in`
				ON `i`.`id` = `in`.`item_id`
			INNER JOIN `vg_items` AS `vgi`
				ON `i`.`id` = `vgi`.`item_id`
			LEFT JOIN `item_descriptions` AS `id`
				ON `vgi`.`version_group_id` = `id`.`version_group_id`
				AND `in`.`language_id` = `id`.`language_id`
				AND `i`.`id` = `id`.`item_id`
			WHERE `vgi`.`version_group_id` = :version_group_id
				AND `vgi`.`is_available` = 1
				AND `in`.`language_id` = :language_id
			ORDER BY `in`.`name`'
		);
		$stmt->bindValue(':version_group_id', $versionGroupId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$dexItems = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$dexItem = new DexItem(
				$result['icon'],
				$result['identifier'],
				$result['name'],
				$result['description'] ?? '',
			);

			$dexItems[] = $dexItem;
		}

		return $dexItems;
	}
}
