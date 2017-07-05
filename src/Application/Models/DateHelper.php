<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use DateInterval;
use DateTime;
use Jp\Dex\Domain\YearMonth;

class DateHelper
{
	/**
	 * Calculate the previous month.
	 *
	 * @param YearMonth $thisMonth
	 *
	 * @return YearMonth
	 */
	public function getPreviousMonth(YearMonth $thisMonth) : YearMonth
	{
		$date = new DateTime();
		$date->setDate($thisMonth->getYear(), $thisMonth->getMonth(), 1);
		$date->sub(new DateInterval('P1M'));
		return new YearMonth(
			(int) $date->format('Y'),
			(int) $date->format('m')
		);
	}
}
