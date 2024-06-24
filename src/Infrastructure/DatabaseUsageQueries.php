<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Stats\Usage\UsageQueriesInterface;
use PDO;

final readonly class DatabaseUsageQueries implements UsageQueriesInterface
{
	public function __construct(
		private PDO $db,
	) {}

	/**
	 * Get the months that have usage records.
	 *
	 * @return DateTime[]
	 */
	public function getMonths() : array
	{
		$stmt = $this->db->prepare(
			'SELECT DISTINCT
				`month`
			FROM `usage`'
		);
		$stmt->execute();

		$months = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$month = new DateTime($result['month']);

			$months[] = $month;
		}

		return $months;
	}

	/**
	 * Get the month of the oldest instance of data in this format.
	 */
	public function getOldest(FormatId $formatId) : ?DateTime
	{
		$stmt = $this->db->prepare(
			'SELECT
				`month`
			FROM `usage`
			WHERE `format_id` = :format_id
			ORDER BY `month`
			LIMIT 1'
		);
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			return null;
		}

		$month = new DateTime($result['month']);

		return $month;
	}

	/**
	 * Get the month of the newest instance of data in this format.
	 */
	public function getNewest(FormatId $formatId) : ?DateTime
	{
		$stmt = $this->db->prepare(
			'SELECT
				`month`
			FROM `usage`
			WHERE `format_id` = :format_id
			ORDER BY `month` DESC
			LIMIT 1'
		);
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			return null;
		}

		$month = new DateTime($result['month']);

		return $month;
	}
}
