<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use DateTime;
use Jp\Dex\Domain\Stats\Usage\UsageQueriesInterface;

final class StatsIndexModel
{
	/** @var DateTime[] $months */
	private(set) array $months = [];


	public function __construct(
		private readonly UsageQueriesInterface $usageQueries,
	) {}


	/**
	 * Set data for the stats index page.
	 */
	public function setData() : void
	{
		$this->months = $this->usageQueries->getMonths();
	}
}
