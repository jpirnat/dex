<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Stats\Usage\UsageRated;
use Jp\Dex\Domain\Stats\Usage\UsageRatedRepositoryInterface;
use PDO;

class DatabaseUsageRatedRepository implements UsageRatedRepositoryInterface
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
	 * Does a usage rated record exist for this month, format, and rating?
	 *
	 * @param DateTime $month
	 * @param FormatId $formatId
	 * @param int $rating
	 *
	 * @return bool
	 */
	public function has(DateTime $month, FormatId $formatId, int $rating) : bool
	{
		$stmt = $this->db->prepare(
			'SELECT
				COUNT(*)
			FROM `usage_rated`
			WHERE `month` = :month
				AND `format_id` = :format_id
				AND `rating` = :rating'
		);
		$stmt->bindValue(':month', $month->format('Y-m-01'), PDO::PARAM_STR);
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
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
				`month`,
				`format_id`,
				`rating`,
				`average_weight_per_team`
			) VALUES (
				:month,
				:format_id,
				:rating,
				:average_weight_per_team
			)'
		);
		$stmt->bindValue(':month', $usageRated->getMonth()->format('Y-m-01'), PDO::PARAM_STR);
		$stmt->bindValue(':format_id', $usageRated->getFormatId()->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rating', $usageRated->getRating(), PDO::PARAM_INT);
		$stmt->bindValue(':average_weight_per_team', $usageRated->getAverageWeightPerTeam(), PDO::PARAM_STR);
		$stmt->execute();
	}
}
