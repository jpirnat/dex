<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

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
}
