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
	 * Insert a `usage_rated` record.
	 *
	 * @param int $year
	 * @param int $month
	 * @param int $metagameId
	 * @param int $rating
	 * @param float $averageWeightPerTeam
	 *
	 * @return bool
	 */
	public function insert(
		int $year,
		int $month,
		int $metagameId,
		int $rating,
		float $averageWeightPerTeam
	) : bool {
		$stmt = $this->db->prepare(
			'INSERT INTO `usage_rated` (
				`year`,
				`month`,
				`metagame_id`,
				`rating`,
				`average_weight_per_team`
			) VALUES (
				:year,
				:month,
				:metagame_id,
				:rating,
				:average_weight_per_team
			)'
		);
		$stmt->bindValue(':year', $year, PDO::PARAM_INT);
		$stmt->bindValue(':month', $month, PDO::PARAM_INT);
		$stmt->bindValue(':metagame_id', $metagameId, PDO::PARAM_INT);
		$stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
		$stmt->bindValue(':average_weight_per_team', $averageWeightPerTeam, PDO::PARAM_STR);
		return $stmt->execute();
	}
}
