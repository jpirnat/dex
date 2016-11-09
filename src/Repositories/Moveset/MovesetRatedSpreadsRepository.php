<?php
declare(strict_types=1);

namespace Jp\Trendalyzer\Repositories\Moveset;

use PDO;

class MovesetRatedSpreadsRepository
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
	 * Insert a `moveset_rated_spreads` record.
	 *
	 * @param int $year
	 * @param int $month
	 * @param int $formatId
	 * @param int $rating
	 * @param int $pokemonId
	 * @param int $natureId
	 * @param int $hp
	 * @param int $atk
	 * @param int $def
	 * @param int $spa
	 * @param int $spd
	 * @param int $spe
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
		int $natureId,
		int $hp,
		int $atk,
		int $def,
		int $spa,
		int $spd,
		int $spe,
		float $percent
	) : bool {
		$stmt = $this->db->prepare(
			'INSERT INTO `moveset_rated_spreads` (
				`year`,
				`month`,
				`format_id`,
				`rating`,
				`pokemon_id`,
				`nature_id`,
				`hp`,
				`atk`,
				`def`,
				`spa`,
				`spd`,
				`spe`,
				`percent`
			) VALUES (
				:year,
				:month,
				:format_id,
				:rating,
				:pokemon_id,
				:nature_id,
				:hp,
				:atk,
				:def,
				:spa,
				:spd,
				:spe,
				:percent
			)'
		);
		$stmt->bindValue(':year', $year, PDO::PARAM_INT);
		$stmt->bindValue(':month', $month, PDO::PARAM_INT);
		$stmt->bindValue(':format_id', $formatId, PDO::PARAM_INT);
		$stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $pokemonId, PDO::PARAM_INT);
		$stmt->bindValue(':nature_id', $natureId, PDO::PARAM_INT);
		$stmt->bindValue(':hp', $hp, PDO::PARAM_INT);
		$stmt->bindValue(':atk', $atk, PDO::PARAM_INT);
		$stmt->bindValue(':def', $def, PDO::PARAM_INT);
		$stmt->bindValue(':spa', $spa, PDO::PARAM_INT);
		$stmt->bindValue(':spd', $spd, PDO::PARAM_INT);
		$stmt->bindValue(':spe', $spe, PDO::PARAM_INT);
		$stmt->bindValue(':percent', $percent, PDO::PARAM_STR);
		return $stmt->execute();
	}
}
