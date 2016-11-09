<?php
declare(strict_types=1);

namespace Jp\Trendalyzer\Repositories\Moveset;

use PDO;

class MovesetRatedMovesRepository
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
	 * Insert a `moveset_rated_moves` record.
	 *
	 * @param int $year
	 * @param int $month
	 * @param int $formatId
	 * @param int $rating
	 * @param int $pokemonId
	 * @param int $moveId
	 * @param float $percent
	 *
	 * @return bool
	 */
	public function insert(
		int $year,
		int $month,
		int $formatId,
		int $rating,
		int $pokemonId,
		int $moveId,
		float $percent
	) : bool {
		$stmt = $this->db->prepare(
			'INSERT INTO `moveset_rated_moves` (
				`year`,
				`month`,
				`format_id`,
				`rating`,
				`pokemon_id`,
				`move_id`,
				`percent`
			) VALUES (
				:year,
				:month,
				:format_id,
				:rating,
				:pokemon_id,
				:move_id,
				:percent
			)'
		);
		$stmt->bindValue(':year', $year, PDO::PARAM_INT);
		$stmt->bindValue(':month', $month, PDO::PARAM_INT);
		$stmt->bindValue(':format_id', $formatId, PDO::PARAM_INT);
		$stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $pokemonId, PDO::PARAM_INT);
		$stmt->bindValue(':move_id', $moveId, PDO::PARAM_INT);
		$stmt->bindValue(':percent', $percent, PDO::PARAM_STR);
		return $stmt->execute();
	}
}
