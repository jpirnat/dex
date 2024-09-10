<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Items\Item;
use Jp\Dex\Domain\Items\ItemId;
use Jp\Dex\Domain\Items\ItemNotFoundException;
use Jp\Dex\Domain\Items\ItemRepositoryInterface;
use PDO;

final readonly class DatabaseItemRepository implements ItemRepositoryInterface
{
	public function __construct(
		private PDO $db,
	) {}

	/**
	 * Get an item by its id.
	 *
	 * @throws ItemNotFoundException if no item exists with this id.
	 */
	public function getById(ItemId $itemId) : Item
	{
		$stmt = $this->db->prepare(
			'SELECT
				`identifier`
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

		return new Item(
			$itemId,
			$result['identifier'],
		);
	}

	/**
	 * Get an item by its identifier.
	 *
	 * @throws ItemNotFoundException if no item exists with this identifier.
	 */
	public function getByIdentifier(string $identifier) : Item
	{
		$stmt = $this->db->prepare(
			'SELECT
				`id`
			FROM `items`
			WHERE `identifier` = :identifier
			LIMIT 1'
		);
		$stmt->bindValue(':identifier', $identifier);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			throw new ItemNotFoundException(
				"No item exists with identifier $identifier."
			);
		}

		return new Item(
			new ItemId($result['id']),
			$identifier,
		);
	}
}
