<?php
declare(strict_types=1);

namespace Jp\Trendalyzer\Repositories\Moveset;

use PDO;

class MovesetRatedItemsRepository
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
	 * Insert a `moveset_rated_items` record.
	 *
	 * @param int $year
	 * @param int $month
	 * @param int $metagameId
	 * @param int $rating
	 * @param int $pokemonId
	 * @param int $itemId
	 * @param float $percent
	 *
	 * @return bool
	 */
	public function insert(
		int $year,
		int $month,
		int $metagameId,
		int $rating,
		int $pokemonId,
		int $itemId,
		float $percent
	) : bool {
		$stmt = $this->db->prepare(
			'INSERT INTO `moveset_rated_items` (
				`year`,
				`month`,
				`metagame_id`,
				`rating`,
				`pokemon_id`,
				`item_id`,
				`percent`
			) VALUES (
				:year,
				:month,
				:metagame_id,
				:rating,
				:pokemon_id,
				:item_id,
				:percent
			)'
		);
		$stmt->bindValue(':year', $year, PDO::PARAM_INT);
		$stmt->bindValue(':month', $month, PDO::PARAM_INT);
		$stmt->bindValue(':metagame_id', $metagameId, PDO::PARAM_INT);
		$stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $pokemonId, PDO::PARAM_INT);
		$stmt->bindValue(':item_id', $itemId, PDO::PARAM_INT);
		$stmt->bindValue(':percent', $percent, PDO::PARAM_STR);
		return $stmt->execute();
	}
}
