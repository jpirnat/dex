<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use DateTime;
use Jp\Dex\Domain\Stats\Usage\UsageQueriesInterface;

final class StatsIndexModel
{
	/** @var DateTime[] $months */
	private array $months = [];


	public function __construct(
		private UsageQueriesInterface $usageQueries,
	) {}


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
