<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Stats\Usage\UsageRated;
use Jp\Dex\Domain\Stats\Usage\UsageRatedRepositoryInterface;
use PDO;

final readonly class DatabaseUsageRatedRepository implements UsageRatedRepositoryInterface
{
	public function __construct(
		private PDO $db,
	) {}

	/**
	 * Does a usage rated record exist for this month, format, and rating?
	 */
	public function has(DateTime $month, FormatId $formatId, int $rating) : bool
	{
		$stmt = $this->db->prepare(
			'SELECT
				1
			FROM `usage_rated`
			WHERE `month` = :month
				AND `format_id` = :format_id
				AND `rating` = :rating
			LIMIT 1'
		);
		$stmt->bindValue(':month', $month->format('Y-m-01'));
		$stmt->bindValue(':format_id', $formatId->value, PDO::PARAM_INT);
		$stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
		$stmt->execute();
		return (bool) $stmt->fetchColumn();
	}

	/**
	 * Save a usage rated record.
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
		$stmt->bindValue(':month', $usageRated->month->format('Y-m-01'));
		$stmt->bindValue(':format_id', $usageRated->formatId->value, PDO::PARAM_INT);
		$stmt->bindValue(':rating', $usageRated->rating, PDO::PARAM_INT);
		$stmt->bindValue(':average_weight_per_team', $usageRated->averageWeightPerTeam);
		$stmt->execute();
	}
}
