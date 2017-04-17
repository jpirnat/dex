<?php
declare(strict_types=1);

namespace Jp\Dex\Stats\Repositories\Leads;

use PDO;
use PDOException;

/**
 * @deprecated
 */
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
	 * Does `leads` contain any records for the given key?
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
			FROM `leads`
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
		try {
			return $stmt->execute();
		} catch (PDOException $e) {
			// A record for this key already exists.
			return false;
		}
	}
}
