<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Moveset\Averaged;

use DateTime;

class MonthsCounter
{
	/**
	 * Count the number of months from start to end.
	 *
	 * @param DateTime $start
	 * @param DateTime $end
	 *
	 * @return int
	 */
	public function countMonths(DateTime $start, DateTime $end) : int
	{
		$startYear = (int) $start->format('Y');
		$startMonth = (int) $start->format('m');
		$endYear = (int) $end->format('Y');
		$endMonth = (int) $end->format('m');
		return ($endYear - $startYear) * 12 + ($endMonth - $startMonth) + 1;
	}
}
