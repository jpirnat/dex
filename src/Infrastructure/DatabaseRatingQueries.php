<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Stats\Usage\RatingQueriesInterface;
use PDO;

final class DatabaseRatingQueries implements RatingQueriesInterface
{
	public function __construct(
		private PDO $db,
	) {}

	/**
	 * Get the ratings for which usage data is available for this month and format.
	 *
	 * @param DateTime $month
	 * @param FormatId $formatId
	 *
	 * @return int[]
	 */
	public function getByMonthAndFormat(DateTime $month, FormatId $formatId) : array
	{
		$stmt = $this->db->prepare(
			'SELECT DISTINCT
				`rating`
			FROM `usage_rated`
			WHERE `month` = :month
				AND `format_id` = :format_id'
		);
		$stmt->bindValue(':month', $month->format('Y-m-01'));
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_COLUMN);
	}

	/**
	 * Get the ratings for which usage data is available between these months,
	 * for this format.
	 *
	 * @param DateTime $start
	 * @param DateTime $end
	 * @param FormatId $formatId
	 *
	 * @return int[]
	 */
	public function getByMonthsAndFormat(DateTime $start, DateTime $end, FormatId $formatId) : array
	{
		$stmt = $this->db->prepare(
			'SELECT DISTINCT
				`rating`
			FROM `usage_rated`
			WHERE `month` BETWEEN :start AND :end
				AND `format_id` = :format_id'
		);
		$stmt->bindValue(':start', $start->format('Y-m-01'));
		$stmt->bindValue(':end', $end->format('Y-m-01'));
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_COLUMN);
	}
}
