<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Stats\Usage\Usage;
use Jp\Dex\Domain\Stats\Usage\UsageRepositoryInterface;
use PDO;

final class DatabaseUsageRepository implements UsageRepositoryInterface
{
	public function __construct(
		private PDO $db,
	) {}

	/**
	 * Does a usage record exist for this month and format?
	 */
	public function has(DateTime $month, FormatId $formatId) : bool
	{
		$stmt = $this->db->prepare(
			'SELECT
				COUNT(*)
			FROM `usage`
			WHERE `month` = :month
				AND `format_id` = :format_id'
		);
		$stmt->bindValue(':month', $month->format('Y-m-01'));
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->execute();
		$count = $stmt->fetchColumn();
		return $count > 0;
	}

	/**
	 * Save a usage record.
	 */
	public function save(Usage $usage) : void
	{
		$stmt = $this->db->prepare(
			'INSERT INTO `usage` (
				`month`,
				`format_id`,
				`total_battles`
			) VALUES (
				:month,
				:format_id,
				:total_battles
			)'
		);
		$stmt->bindValue(':month', $usage->getMonth()->format('Y-m-01'));
		$stmt->bindValue(':format_id', $usage->getFormatId()->value(), PDO::PARAM_INT);
		$stmt->bindValue(':total_battles', $usage->getTotalBattles(), PDO::PARAM_INT);
		$stmt->execute();
	}
}
