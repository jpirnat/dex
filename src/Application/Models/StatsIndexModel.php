<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use DateTime;
use Jp\Dex\Domain\Stats\Usage\UsageQueriesInterface;

class StatsIndexModel
{
	/** @var UsageQueriesInterface $usageQueries */
	private $usageQueries;


	/** @var DateTime[] $months */
	private $months = [];


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
	 * Set the months that have usage data.
	 *
	 * @return void
	 */
	public function setMonths() : void
	{
		$this->months = $this->usageQueries->getMonths();
	}

	/**
	 * Get the months that have usage data.
	 *
	 * @return DateTime[]
	 */
	public function getMonths() : array
	{
		return $this->months;
	}
}
