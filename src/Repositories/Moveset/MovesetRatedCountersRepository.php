<?php
declare(strict_types=1);

namespace Jp\Trendalyzer\Repositories\Moveset;

use PDO;

class MovesetRatedCountersRepository
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
	 * Insert a `moveset_rated_counters` record.
	 *
	 * @param int $year
	 * @param int $month
	 * @param int $metagameId
	 * @param int $rating
	 * @param int $pokemonId
	 * @param int $counterId
	 * @param float $percent
	 * @param float $percentKnockedOut
	 * @param float $percentSwitchedOut
	 *
	 * @return bool
	 */
	public function insert(
		int $year,
		int $month,
		int $metagameId,
		int $rating,
		int $pokemonId,
		int $counterId,
		float $percent,
		float $percentKnockedOut,
		float $percentSwitchedOut
	) : bool {
		$stmt = $this->db->prepare(
			'INSERT INTO `moveset_rated_counters` (
				`year`,
				`month`,
				`metagame_id`,
				`rating`,
				`pokemon_id`,
				`counter_id`,
				`percent`,
				`percent_knocked_out`,
				`percent_switched_out`
			) VALUES (
				:year,
				:month,
				:metagame_id,
				:rating,
				:pokemon_id,
				:counter_id,
				:percent,
				:percent_knocked_out,
				:percent_switched_out
			)'
		);
		$stmt->bindValue(':year', $year, PDO::PARAM_INT);
		$stmt->bindValue(':month', $month, PDO::PARAM_INT);
		$stmt->bindValue(':metagame_id', $metagameId, PDO::PARAM_INT);
		$stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $pokemonId, PDO::PARAM_INT);
		$stmt->bindValue(':counter_id', $counterId, PDO::PARAM_INT);
		$stmt->bindValue(':percent', $percent, PDO::PARAM_STR);
		$stmt->bindValue(':percent_knocked_out', $percentKnockedOut, PDO::PARAM_STR);
		$stmt->bindValue(':percent_switched_out', $percentSwitchedOut, PDO::PARAM_STR);
		return $stmt->execute();
	}
}
