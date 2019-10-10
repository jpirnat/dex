<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Stats\Leads\Leads;
use Jp\Dex\Domain\Stats\Leads\LeadsRepositoryInterface;
use PDO;

final class DatabaseLeadsRepository implements LeadsRepositoryInterface
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
	 * Does a leads record exist for this month and format?
	 *
	 * @param DateTime $month
	 * @param FormatId $formatId
	 *
	 * @return bool
	 */
	public function has(DateTime $month, FormatId $formatId) : bool
	{
		$stmt = $this->db->prepare(
			'SELECT
				COUNT(*)
			FROM `leads`
			WHERE `month` = :month
				AND `format_id` = :format_id'
		);
		$stmt->bindValue(':month', $month->format('Y-m-01'), PDO::PARAM_STR);
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
				`month`,
				`format_id`,
				`total_leads`
			) VALUES (
				:month,
				:format_id,
				:total_leads
			)'
		);
		$stmt->bindValue(':month', $leads->getMonth()->format('Y-m-01'), PDO::PARAM_STR);
		$stmt->bindValue(':format_id', $leads->getFormatId()->value(), PDO::PARAM_INT);
		$stmt->bindValue(':total_leads', $leads->getTotalLeads(), PDO::PARAM_INT);
		$stmt->execute();
	}
}
