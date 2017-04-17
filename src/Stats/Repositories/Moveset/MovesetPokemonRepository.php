<?php
declare(strict_types=1);

namespace Jp\Dex\Stats\Repositories\Moveset;

use PDO;
use PDOException;

/**
 * @deprecated
 */
class MovesetPokemonRepository
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
	 * Does `moveset_pokemon` contain any records for the given key?
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
			FROM `moveset_pokemon`
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
	 * Insert a `moveset_pokemon` record.
	 *
	 * @param int $year
	 * @param int $month
	 * @param int $formatId
	 * @param int $pokemonId
	 * @param int $rawCount
	 * @param int|null $viabilityCeiling
	 *
	 * @return bool
	 */
	public function insert(
		int $year,
		int $month,
		int $formatId,
		int $pokemonId,
		int $rawCount,
		?int $viabilityCeiling
	) : bool {
		if ($viabilityCeiling === null) {
			// Viability ceiling can be null.
			$viabilityCeilingType = PDO::PARAM_NULL;
		} else {
			$viabilityCeilingType = PDO::PARAM_INT;
		}

		$stmt = $this->db->prepare(
			'INSERT INTO `moveset_pokemon` (
				`year`,
				`month`,
				`format_id`,
				`pokemon_id`,
				`raw_count`,
				`viability_ceiling`
			) VALUES (
				:year,
				:month,
				:format_id,
				:pokemon_id,
				:raw_count,
				:viability_ceiling
			)'
		);
		$stmt->bindValue(':year', $year, PDO::PARAM_INT);
		$stmt->bindValue(':month', $month, PDO::PARAM_INT);
		$stmt->bindValue(':format_id', $formatId, PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $pokemonId, PDO::PARAM_INT);
		$stmt->bindValue(':raw_count', $rawCount, PDO::PARAM_INT);
		$stmt->bindValue(':viability_ceiling', $viabilityCeiling, $viabilityCeilingType);
		try {
			return $stmt->execute();
		} catch (PDOException $e) {
			// A record for this key already exists.
			return false;
		}
	}
}
