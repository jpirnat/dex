<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Stats\Moveset\MovesetRatedItem;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedItemRepositoryInterface;
use PDO;
use PDOException;

final class DatabaseMovesetRatedItemRepository implements MovesetRatedItemRepositoryInterface
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
	 * Save a moveset rated item record.
	 *
	 * @param MovesetRatedItem $movesetRatedItem
	 *
	 * @return void
	 */
	public function save(MovesetRatedItem $movesetRatedItem) : void
	{
		$stmt = $this->db->prepare(
			'INSERT INTO `moveset_rated_items` (
				`month`,
				`format_id`,
				`rating`,
				`pokemon_id`,
				`item_id`,
				`percent`
			) VALUES (
				:month,
				:format_id,
				:rating,
				:pokemon_id,
				:item_id,
				:percent
			)'
		);
		$stmt->bindValue(':month', $movesetRatedItem->getMonth()->format('Y-m-01'), PDO::PARAM_STR);
		$stmt->bindValue(':format_id', $movesetRatedItem->getFormatId()->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rating', $movesetRatedItem->getRating(), PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $movesetRatedItem->getPokemonId()->value(), PDO::PARAM_INT);
		$stmt->bindValue(':item_id', $movesetRatedItem->getItemId()->value(), PDO::PARAM_INT);
		$stmt->bindValue(':percent', $movesetRatedItem->getPercent(), PDO::PARAM_STR);
		try {
			$stmt->execute();
		} catch (PDOException $e) {
			// This record already exists.
			// Bug fix for https://www.smogon.com/stats/2019-11
			// in which Leek and Stick both appear, during the transition to gen 8.
		}
	}
}
