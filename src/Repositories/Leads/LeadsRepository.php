<?php
declare(strict_types=1);

namespace Jp\Trendalyzer\Repositories\Leads;

use PDO;

class LeadsRepository
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
	 * Insert a `leads` record.
	 *
	 * @param int $year
	 * @param int $month
	 * @param int $formatId
	 * @param int $totalLeads
	 *
	 * @return bool
	 */
	public function insert(
		int $year,
		int $month,
		int $formatId,
		int $totalLeads
	) : bool {
		$stmt = $this->db->prepare(
			'INSERT INTO `leads` (
				`year`,
				`month`,
				`format_id`,
				`total_leads`
			) VALUES (
				:year,
				:month,
				:format_id,
				:total_leads
			)'
		);
		$stmt->bindValue(':year', $year, PDO::PARAM_INT);
		$stmt->bindValue(':month', $month, PDO::PARAM_INT);
		$stmt->bindValue(':format_id', $formatId, PDO::PARAM_INT);
		$stmt->bindValue(':total_leads', $totalLeads, PDO::PARAM_INT);
		return $stmt->execute();
	}
}
