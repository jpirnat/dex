<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Stats\Leads\Leads;
use Jp\Dex\Domain\Stats\Leads\LeadsRepositoryInterface;
use PDO;

final readonly class DatabaseLeadsRepository implements LeadsRepositoryInterface
{
	public function __construct(
		private PDO $db,
	) {}

	/**
	 * Does a leads record exist for this month and format?
	 */
	public function has(DateTime $month, FormatId $formatId) : bool
	{
		$stmt = $this->db->prepare(
			'SELECT
				1
			FROM `leads`
			WHERE `month` = :month
				AND `format_id` = :format_id
			LIMIT 1'
		);
		$stmt->bindValue(':month', $month->format('Y-m-01'));
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->execute();
		return (bool) $stmt->fetchColumn();
	}

	/**
	 * Save a leads rated Pokémon record.
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
		$stmt->bindValue(':month', $leads->getMonth()->format('Y-m-01'));
		$stmt->bindValue(':format_id', $leads->getFormatId()->value(), PDO::PARAM_INT);
		$stmt->bindValue(':total_leads', $leads->getTotalLeads(), PDO::PARAM_INT);
		$stmt->execute();
	}
}
