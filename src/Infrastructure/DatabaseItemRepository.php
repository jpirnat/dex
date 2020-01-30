<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Items\Item;
use Jp\Dex\Domain\Items\ItemFlingEffectId;
use Jp\Dex\Domain\Items\ItemId;
use Jp\Dex\Domain\Items\ItemNotFoundException;
use Jp\Dex\Domain\Items\ItemRepositoryInterface;
use Jp\Dex\Domain\Versions\VersionGroupId;
use PDO;

final class DatabaseItemRepository implements ItemRepositoryInterface
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
	 * Get an item by its id.
	 *
	 * @param ItemId $itemId
	 *
	 * @throws ItemNotFoundException if no item exists with this id.
	 *
	 * @return Item
	 */
	public function getById(ItemId $itemId) : Item
	{
		$stmt = $this->db->prepare(
			'SELECT
				`identifier`,
				`introduced_in_version_group_id`,
				`item_fling_power`,
				`item_fling_effect_id`
			FROM `items`
			WHERE `id` = :item_id
			LIMIT 1'
		);
		$stmt->bindValue(':item_id', $itemId->value(), PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			throw new ItemNotFoundException(
				'No item exists with id ' . $itemId->value() . '.'
			);
		}

		if ($result['item_fling_effect_id'] !== null) {
			$itemFlingEffectId = new ItemFlingEffectId($result['item_fling_effect_id']);
		} else {
			// The item has no fling effect.
			$itemFlingEffectId = null;
		}

		$item = new Item(
			$itemId,
			$result['identifier'],
			new VersionGroupId($result['introduced_in_version_group_id']),
			$result['item_fling_power'],
			$itemFlingEffectId
		);

		return $item;
	}

	/**
	 * Get an item by its identifier.
	 *
	 * @param string $identifier
	 *
	 * @throws ItemNotFoundException if no item exists with this identifier.
	 *
	 * @return Item
	 */
	public function getByIdentifier(string $identifier) : Item
	{
		$stmt = $this->db->prepare(
			'SELECT
				`id`,
				`introduced_in_version_group_id`,
				`item_fling_power`,
				`item_fling_effect_id`
			FROM `items`
			WHERE `identifier` = :identifier
			LIMIT 1'
		);
		$stmt->bindValue(':identifier', $identifier, PDO::PARAM_STR);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			throw new ItemNotFoundException(
				'No item exists with identifier ' . $identifier . '.'
			);
		}

		if ($result['item_fling_effect_id'] !== null) {
			$itemFlingEffectId = new ItemFlingEffectId($result['item_fling_effect_id']);
		} else {
			// The item has no fling effect.
			$itemFlingEffectId = null;
		}

		$item = new Item(
			new ItemId($result['id']),
			$identifier,
			new VersionGroupId($result['introduced_in_version_group_id']),
			$result['item_fling_power'],
			$itemFlingEffectId
		);

		return $item;
	}
}
