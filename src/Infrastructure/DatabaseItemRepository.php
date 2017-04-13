<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Exception;
use Jp\Dex\Domain\Items\Item;
use Jp\Dex\Domain\Items\ItemFlingEffectId;
use Jp\Dex\Domain\Items\ItemId;
use Jp\Dex\Domain\Items\ItemRepositoryInterface;
use Jp\Dex\Domain\Versions\Generation;
use PDO;

class DatabaseItemRepository implements ItemRepositoryInterface
{
	/** @var PDO $db */
	protected $db;

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
	 * Get an ability by its identifier.
	 *
	 * @param string $identifier
	 *
	 * @throws Exception if no ability exists with this identifier.
	 *
	 * @return Item
	 */
	public function getByIdentifier(string $identifier) : Item
	{
		$stmt = $this->db->prepare(
			'SELECT
				`id`,
				`introduced_in_generation`,
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
			throw new Exception('No item exists with identifier ' . $identifier);
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
			new Generation($result['introduced_in_generation']),
			$result['item_fling_power'],
			$itemFlingEffectId
		);

		return $item;
	}
}
