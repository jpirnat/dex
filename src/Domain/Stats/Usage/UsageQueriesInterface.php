<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Usage;

use Jp\Dex\Domain\YearMonth;

interface UsageQueriesInterface
{
	/**
	 * Get the year/month combinations that have usage records.
	 *
	 * @return YearMonth[]
	 */
	public function getYearMonths() : array;
}
