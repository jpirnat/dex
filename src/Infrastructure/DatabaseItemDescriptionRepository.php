<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Items\ItemDescription;
use Jp\Dex\Domain\Items\ItemDescriptionRepositoryInterface;
use Jp\Dex\Domain\Items\ItemId;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Versions\VersionGroupId;
use PDO;

final readonly class DatabaseItemDescriptionRepository implements ItemDescriptionRepositoryInterface
{
	public function __construct(
		private PDO $db,
	) {}

	/**
	 * Get an item description by version group, language, and item.
	 */
	public function getByItem(
		VersionGroupId $versionGroupId,
		LanguageId $languageId,
		ItemId $itemId,
	) : ItemDescription {
		$stmt = $this->db->prepare(
			'SELECT
				`name`,
				`description`
			FROM `item_descriptions`
			WHERE `version_group_id` = :version_group_id
				AND `language_id` = :language_id
				AND `item_id` = :item_id
			LIMIT 1'
		);
		$stmt->bindValue(':version_group_id', $versionGroupId->value, PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value, PDO::PARAM_INT);
		$stmt->bindValue(':item_id', $itemId->value, PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			return new ItemDescription($versionGroupId, $languageId, $itemId, '', '');
		}

		return new ItemDescription(
			$versionGroupId,
			$languageId,
			$itemId,
			$result['name'],
			$result['description'],
		);
	}

	/**
	 * Get item descriptions for TMs/HMs/TRs available for this version group,
	 * based on all the version groups that can transfer movesets into this one.
	 *
	 * @return ItemDescription[][] Indexed by version group id, then item id.
	 */
	public function getTmsByIntoVg(
		VersionGroupId $versionGroupId,
		LanguageId $languageId,
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`i`.`version_group_id`,
				`i`.`item_id`,
				`i`.`name`,
				`i`.`description`
			FROM `technical_machines` AS `t`
			INNER JOIN `item_descriptions` AS `i`
				ON `t`.`version_group_id` = `i`.`version_group_id`
				AND `t`.`item_id` = `i`.`item_id`
			WHERE `i`.`version_group_id` IN (
				SELECT
					`from_vg_id`
				FROM `vg_move_transfers`
				WHERE `into_vg_id` = :version_group_id
			)
			AND `i`.`language_id` = :language_id'
		);
		$stmt->bindValue(':version_group_id', $versionGroupId->value, PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value, PDO::PARAM_INT);
		$stmt->execute();

		$itemDescriptions = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$itemDescription = new ItemDescription(
				new VersionGroupId($result['version_group_id']),
				$languageId,
				new ItemId($result['item_id']),
				$result['name'],
				$result['description'],
			);

			$itemDescriptions[$result['version_group_id']][$result['item_id']] = $itemDescription;
		}

		return $itemDescriptions;
	}

	/**
	 * Get item descriptions for TMs/HMs/TRs for this specific move and
	 * available for this version group, based on all the version groups that
	 * can transfer movesets into this one.
	 *
	 * @return ItemDescription[][] Indexed by version group id, then item id.
	 */
	public function getTmsByIntoVgAndMove(
		VersionGroupId $versionGroupId,
		MoveId $moveId,
		LanguageId $languageId,
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`i`.`version_group_id`,
				`i`.`item_id`,
				`i`.`name`,
				`i`.`description`
			FROM `technical_machines` AS `t`
			INNER JOIN `item_descriptions` AS `i`
				ON `t`.`version_group_id` = `i`.`version_group_id`
				AND `t`.`item_id` = `i`.`item_id`
			WHERE `i`.`version_group_id` IN (
				SELECT
					`from_vg_id`
				FROM `vg_move_transfers`
				WHERE `into_vg_id` = :version_group_id
			)
			AND `t`.`move_id` = :move_id
			AND `i`.`language_id` = :language_id'
		);
		$stmt->bindValue(':version_group_id', $versionGroupId->value, PDO::PARAM_INT);
		$stmt->bindValue(':move_id', $moveId->value, PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value, PDO::PARAM_INT);
		$stmt->execute();

		$itemDescriptions = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$itemDescription = new ItemDescription(
				new VersionGroupId($result['version_group_id']),
				$languageId,
				new ItemId($result['item_id']),
				$result['name'],
				$result['description'],
			);

			$itemDescriptions[$result['version_group_id']][$result['item_id']] = $itemDescription;
		}

		return $itemDescriptions;
	}
}
