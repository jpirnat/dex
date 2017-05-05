<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Stats\Leads\Leads;
use Jp\Dex\Domain\Stats\Leads\LeadsRepositoryInterface;
use PDO;

class DatabaseLeadsRepository implements LeadsRepositoryInterface
{
	/** @var PDO $db */
	private $db;

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
	 * Do any leads records exist for this year, month, and format?
	 *
	 * @param int $year
	 * @param int $month
	 * @param FormatId $formatId
	 *
	 * @return bool
	 */
	public function has(
		int $year,
		int $month,
		FormatId $formatId
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
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->execute();
		$count = $stmt->fetchColumn();
		return $count > 0;
	}

	/**
	 * Save a leads rated Pokémon record.
	 *
	 * @param Leads $leads
	 *
	 * @return void
	 */
	public function save(Leads $leads) : void
	{
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
		$stmt->bindValue(':year', $leads->year(), PDO::PARAM_INT);
		$stmt->bindValue(':month', $leads->month(), PDO::PARAM_INT);
		$stmt->bindValue(':format_id', $leads->formatId()->value(), PDO::PARAM_INT);
		$stmt->bindValue(':total_leads', $leads->totalLeads(), PDO::PARAM_INT);
		$stmt->execute();
	}
}
