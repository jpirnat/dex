<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Stats\Usage\Usage;
use Jp\Dex\Domain\Stats\Usage\UsageRepositoryInterface;
use PDO;

class DatabaseUsageRepository implements UsageRepositoryInterface
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
	 * Do any usage records exist for this year, month, and format?
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
			FROM `usage`
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
	 * Save a usage record.
	 *
	 * @param Usage $usage
	 *
	 * @return void
	 */
	public function save(Usage $usage) : void
	{
		$stmt = $this->db->prepare(
			'INSERT INTO `usage` (
				`year`,
				`month`,
				`format_id`,
				`total_battles`
			) VALUES (
				:year,
				:month,
				:format_id,
				:total_battles
			)'
		);
		$stmt->bindValue(':year', $usage->year(), PDO::PARAM_INT);
		$stmt->bindValue(':month', $usage->month(), PDO::PARAM_INT);
		$stmt->bindValue(':format_id', $usage->formatId()->value(), PDO::PARAM_INT);
		$stmt->bindValue(':total_battles', $usage->totalBattles(), PDO::PARAM_INT);
		$stmt->execute();
	}
}
