<?php
declare(strict_types=1);

namespace Jp\Dex\Stats\Repositories\Usage;

use PDO;
use PDOException;

/**
 * @deprecated
 */
class UsagePokemonRepository
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
	 * Does `usage_pokemon` contain any records for the given key?
	 * (Was the relevant data already imported?)
	 *
	 * @param int $year
	 * @param int $month
	 * @param int $formatId
	 *
	 * @return bool
	 */
	public function exists(
		int $year,
		int $month,
		int $formatId
	) : bool {
		$stmt = $this->db->prepare(
			'SELECT
				COUNT(*)
			FROM `usage_pokemon`
			WHERE `year` = :year
				AND `month` = :month
				AND `format_id` = :format_id'
		);
		$stmt->bindValue(':year', $year, PDO::PARAM_INT);
		$stmt->bindValue(':month', $month, PDO::PARAM_INT);
		$stmt->bindValue(':format_id', $formatId, PDO::PARAM_INT);
		$stmt->execute();
		$count = $stmt->fetchColumn();
		return $count > 0;
	}

	/**
	 * Insert a `usage_pokemon` record.
	 *
	 * @param int $year
	 * @param int $month
	 * @param int $formatId
	 * @param int $pokemonId
	 * @param int $raw
	 * @param float $rawPercent
	 * @param int $real
	 * @param float $realPercent
	 *
	 * @return bool
	 */
	public function insert(
		int $year,
		int $month,
		int $formatId,
		int $pokemonId,
		int $raw,
		float $rawPercent,
		int $real,
		float $realPercent
	) : bool {
		$stmt = $this->db->prepare(
			'INSERT INTO `usage_pokemon` (
				`year`,
				`month`,
				`format_id`,
				`pokemon_id`,
				`raw`,
				`raw_percent`,
				`real`,
				`real_percent`
			) VALUES (
				:year,
				:month,
				:format_id,
				:pokemon_id,
				:raw,
				:raw_percent,
				:real,
				:real_percent
			)'
		);
		$stmt->bindValue(':year', $year, PDO::PARAM_INT);
		$stmt->bindValue(':month', $month, PDO::PARAM_INT);
		$stmt->bindValue(':format_id', $formatId, PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $pokemonId, PDO::PARAM_INT);
		$stmt->bindValue(':raw', $raw, PDO::PARAM_INT);
		$stmt->bindValue(':raw_percent', $rawPercent, PDO::PARAM_STR);
		$stmt->bindValue(':real', $real, PDO::PARAM_INT);
		$stmt->bindValue(':real_percent', $realPercent, PDO::PARAM_STR);
		try {
			return $stmt->execute();
		} catch (PDOException $e) {
			// A record for this key already exists.
			return false;
		}
	}
}
