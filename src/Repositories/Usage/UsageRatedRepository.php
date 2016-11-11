<?php
declare(strict_types=1);

namespace Jp\Trendalyzer\Repositories\Usage;

use PDO;

class UsageRatedRepository
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
	 * Does `usage_rated` contain any records for the given key?
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
			FROM `usage_rated`
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
	 * Insert a `usage_rated` record.
	 *
	 * @param int $year
	 * @param int $month
	 * @param int $formatId
	 * @param int $rating
	 * @param float $averageWeightPerTeam
	 *
	 * @return bool
	 */
	public function insert(
		int $year,
		int $month,
		int $formatId,
		int $rating,
		float $averageWeightPerTeam
	) : bool {
		$stmt = $this->db->prepare(
			'INSERT INTO `usage_rated` (
				`year`,
				`month`,
				`format_id`,
				`rating`,
				`average_weight_per_team`
			) VALUES (
				:year,
				:month,
				:format_id,
				:rating,
				:average_weight_per_team
			)'
		);
		$stmt->bindValue(':year', $year, PDO::PARAM_INT);
		$stmt->bindValue(':month', $month, PDO::PARAM_INT);
		$stmt->bindValue(':format_id', $formatId, PDO::PARAM_INT);
		$stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
		$stmt->bindValue(':average_weight_per_team', $averageWeightPerTeam, PDO::PARAM_STR);
		return $stmt->execute();
	}
}
