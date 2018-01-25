<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use Jp\Dex\Domain\Stats\Usage\UsageQueriesInterface;
use Jp\Dex\Domain\YearMonth;

class StatsIndexModel
{
	/** @var UsageQueriesInterface $usageQueries */
	private $usageQueries;

	/** @var YearMonth[] $yearMonths */
	private $yearMonths = [];

	/**
	 * Constructor.
	 *
	 * @param UsageQueriesInterface $usageQueries
	 */
	public function __construct(UsageQueriesInterface $usageQueries)
	{
		$this->usageQueries = $usageQueries;
	}

	/**
	 * Set the year/month combinations that have usage data.
	 *
	 * @return void
	 */
	public function setYearMonths() : void
	{
		$this->yearMonths = $this->usageQueries->getYearMonths();
	}

	/**
	 * Get the year/month combinations that have usage data.
	 *
	 * @return YearMonth[]
	 */
	public function getYearMonths() : array
	{
		return $this->yearMonths;
	}
}
