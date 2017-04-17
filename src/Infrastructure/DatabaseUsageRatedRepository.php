<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Stats\Usage\UsageRated;
use Jp\Dex\Domain\Stats\Usage\UsageRatedRepositoryInterface;
use PDO;

class DatabaseUsageRatedRepository implements UsageRatedRepositoryInterface
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
	 * Do any usage rated records exist for this year, month, format, and
	 * rating?
	 *
	 * @param int $year
	 * @param int $month
	 * @param FormatId $formatId
	 * @param int $rating
	 *
	 * @return bool
	 */
	public function has(
		int $year,
		int $month,
		FormatId $formatId,
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
	 * Save a usage rated record.
	 *
	 * @param UsageRated $usageRated
	 *
	 * @return void
	 */
	public function save(UsageRated $usageRated) : void
	{
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
		$stmt->bindValue(':year', $usageRated->year(), PDO::PARAM_INT);
		$stmt->bindValue(':month', $usageRated->month(), PDO::PARAM_INT);
		$stmt->bindValue(':format_id', $usageRated->formatId()->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rating', $usageRated->rating(), PDO::PARAM_INT);
		$stmt->bindValue(':average_weight_per_team', $usageRated->averageWeightPerTeam(), PDO::PARAM_STR);
		$stmt->execute();
	}
}
