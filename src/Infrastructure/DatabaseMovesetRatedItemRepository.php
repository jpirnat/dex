<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Stats\Moveset\MovesetRatedItem;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedItemRepositoryInterface;
use PDO;
use PDOException;

final class DatabaseMovesetRatedItemRepository implements MovesetRatedItemRepositoryInterface
{
	public function __construct(
		private PDO $db,
	) {}

	/**
	 * Save a moveset rated item record.
	 */
	public function save(MovesetRatedItem $movesetRatedItem) : void
	{
		$stmt = $this->db->prepare(
			'INSERT INTO `moveset_rated_items` (
				`usage_rated_pokemon_id`,
				`item_id`,
				`percent`
			) VALUES (
				:urp_id,
				:item_id,
				:percent
			)'
		);
		$stmt->bindValue(':urp_id', $movesetRatedItem->getUsageRatedPokemonId()->value(), PDO::PARAM_INT);
		$stmt->bindValue(':item_id', $movesetRatedItem->getItemId()->value(), PDO::PARAM_INT);
		$stmt->bindValue(':percent', $movesetRatedItem->getPercent());
		try {
			$stmt->execute();
		} catch (PDOException) {
			// This record already exists.
			// Bug fix for https://www.smogon.com/stats/2019-11
			// in which Leek and Stick both appear, during the transition to gen 8.
		}
	}
}
