<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Stats\Usage\UsageRatedQueriesInterface;
use PDO;

final readonly class DatabaseUsageRatedQueries implements UsageRatedQueriesInterface
{
	public function __construct(
		private PDO $db,
	) {}

	/**
	 * Get the format/rating combinations for this month.
	 *
	 * @return array An array of the form [
	 *     [
	 *         'formatId' => FormatId
	 *         'rating' => int
	 *     ],
	 *     ...
	 * ]
	 */
	public function getFormatRatings(DateTime $month) : array
	{
		$stmt = $this->db->prepare(
			'SELECT
				`format_id`,
				`rating`
			FROM `usage_rated`
			WHERE `month` = :month'
		);
		$stmt->bindValue(':month', $month->format('Y-m-01'));
		$stmt->execute();

		$formatRatings = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$formatRatings[] = [
				'formatId' => new FormatId($result['format_id']),
				'rating' => $result['rating'],
			];
		}

		return $formatRatings;
	}

	/**
	 * Get the months that have data recorded for this format and rating.
	 *
	 * @return DateTime[]
	 */
	public function getMonthsWithData(FormatId $formatId, int $rating) : array
	{
		$stmt = $this->db->prepare(
			'SELECT
				`month`
			FROM `usage_rated`
			WHERE `format_id` = :format_id
				AND `rating` = :rating
			ORDER BY `month`'
		);
		$stmt->bindValue(':format_id', $formatId->value, PDO::PARAM_INT);
		$stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
		$stmt->execute();

		$months = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$months[] = new DateTime($result['month']);
		}

		return $months;
	}
}
