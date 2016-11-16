<?php
declare(strict_types=1);

namespace Jp\Trendalyzer\Repositories\Moveset;

use PDO;
use PDOException;

class MovesetRatedPokemonRepository
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
	 * Does `moveset_rated_pokemon` contain any records for the given key?
	 * (Was the relevant data already imported?)
	 *
	 * @param int $year
	 * @param int $month
	 * @param int $formatId
	 * @param int $rating
	 *
	 * @return bool
	 */
	public function exists(
		int $year,
		int $month,
		int $formatId,
		int $rating
	) : bool {
		$stmt = $this->db->prepare(
			'SELECT
				COUNT(*)
			FROM `moveset_rated_pokemon`
			WHERE `year` = :year
				AND `month` = :month
				AND `format_id` = :format_id
				AND `rating` = :rating'
		);
		$stmt->bindValue(':year', $year, PDO::PARAM_INT);
		$stmt->bindValue(':month', $month, PDO::PARAM_INT);
		$stmt->bindValue(':format_id', $formatId, PDO::PARAM_INT);
		$stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
		$stmt->execute();
		$count = $stmt->fetchColumn();
		return $count > 0;
	}

	/**
	 * Insert a `moveset_rated_pokemon` record.
	 *
	 * @param int $year
	 * @param int $month
	 * @param int $formatId
	 * @param int $rating
	 * @param int $pokemonId
	 * @param float $averageWeight
	 *
	 * @return bool
	 */
	public function insert(
		int $year,
		int $month,
		int $formatId,
		int $rating,
		int $pokemonId,
		float $averageWeight
	) : bool {
		$stmt = $this->db->prepare(
			'INSERT INTO `moveset_rated_pokemon` (
				`year`,
				`month`,
				`format_id`,
				`rating`,
				`pokemon_id`,
				`average_weight`
			) VALUES (
				:year,
				:month,
				:format_id,
				:rating,
				:pokemon_id,
				:average_weight
			)'
		);
		$stmt->bindValue(':year', $year, PDO::PARAM_INT);
		$stmt->bindValue(':month', $month, PDO::PARAM_INT);
		$stmt->bindValue(':format_id', $formatId, PDO::PARAM_INT);
		$stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $pokemonId, PDO::PARAM_INT);
		$stmt->bindValue(':average_weight', $averageWeight, PDO::PARAM_STR);
		try {
			return $stmt->execute();
		} catch (PDOException $e) {
			// A record for this key already exists.
			return false;
		}
	}
}
