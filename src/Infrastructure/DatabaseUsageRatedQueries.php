<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Stats\Usage\UsageRatedQueriesInterface;
use PDO;

class DatabaseUsageRatedQueries implements UsageRatedQueriesInterface
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
	 * Get the format/rating combinations for this year and month.
	 *
	 * @param int $year
	 * @param int $month
	 *
	 * @return array An array of the form [
	 *     [
	 *         'formatId' => FormatId
	 *         'rating' => int
	 *     ],
	 *     ...
	 * ]
	 */
	public function getFormatRatings(int $year, int $month) : array
	{
		$stmt = $this->db->prepare(
			'SELECT
				`format_id`,
				`rating`
			FROM `usage_rated`
			WHERE `year` = :year
				AND `month` = :month'
		);
		$stmt->bindValue(':year', $year, PDO::PARAM_INT);
		$stmt->bindValue(':month', $month, PDO::PARAM_INT);
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
