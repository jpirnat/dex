<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Stats\Usage\UsageRatedQueriesInterface;
use PDO;

final class DatabaseUsageRatedQueries implements UsageRatedQueriesInterface
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
}
