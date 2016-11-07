<?php
declare(strict_types=1);

namespace Jp\Trendalyzer\Repositories\Usage;

use PDO;

class UsageRepository
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
	 * Insert a `usage` record.
	 *
	 * @param int $year
	 * @param int $month
	 * @param int $metagameId
	 * @param int $totalBattles
	 *
	 * @return bool
	 */
	public function insert(
		int $year,
		int $month,
		int $metagameId,
		int $totalBattles
	) : bool {
		$stmt = $this->db->prepare(
			'INSERT INTO `usage` (
				`year`,
				`month`,
				`metagame_id`,
				`total_battles`
			) VALUES (
				:year,
				:month,
				:metagame_id,
				:total_battles
			)'
		);
		$stmt->bindValue(':year', $year, PDO::PARAM_INT);
		$stmt->bindValue(':month', $month, PDO::PARAM_INT);
		$stmt->bindValue(':metagame_id', $metagameId, PDO::PARAM_INT);
		$stmt->bindValue(':total_battles', $totalBattles, PDO::PARAM_INT);
		return $stmt->execute();
	}
}
