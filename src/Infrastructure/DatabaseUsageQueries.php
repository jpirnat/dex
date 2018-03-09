<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Stats\Usage\UsageQueriesInterface;
use Jp\Dex\Domain\YearMonth;
use PDO;

class DatabaseUsageQueries implements UsageQueriesInterface
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
	 * Get the year/month combinations that have usage records.
	 *
	 * @return YearMonth[]
	 */
	public function getYearMonths() : array
	{
		$stmt = $this->db->prepare(
			'SELECT DISTINCT
				`year`,
				`month`
			FROM `usage`'
		);
		$stmt->execute();

		$yearMonths = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$yearMonth = new YearMonth($result['year'], $result['month']);

			$yearMonths[] = $yearMonth;
		}

		return $yearMonths;
	}

	/**
	 * Get the year/month of the oldest instance of data in this format.
	 *
	 * @param FormatId $formatId
	 *
	 * @return YearMonth|null
	 */
	public function getOldest(FormatId $formatId) : ?YearMonth
	{
		$stmt = $this->db->prepare(
			'SELECT
				`year`,
				`month`
			FROM `usage`
			WHERE `format_id` = :format_id
			ORDER BY
				`year` ASC,
				`month` ASC
			LIMIT 1'
		);
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			return null;
		}

		$yearMonth = new YearMonth($result['year'], $result['month']);

		return $yearMonth;
	}

	/**
	 * Get the year/month of the newest instance of data in this format.
	 *
	 * @param FormatId $formatId
	 *
	 * @return YearMonth|null
	 */
	public function getNewest(FormatId $formatId) : ?YearMonth
	{
		$stmt = $this->db->prepare(
			'SELECT
				`year`,
				`month`
			FROM `usage`
			WHERE `format_id` = :format_id
			ORDER BY
				`year` DESC,
				`month` DESC
			LIMIT 1'
		);
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			return null;
		}

		$yearMonth = new YearMonth($result['year'], $result['month']);

		return $yearMonth;
	}
}
