<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats;

use DateTime;
use Jp\Dex\Domain\Stats\Exceptions\InvalidMonthException;

trait ValidateMonthTrait
{
	/**
	 * Validate the month for a usage data entity.
	 *
	 * @throws InvalidMonthException if $month is invalid.
	 */
	public function validateMonth(DateTime $month) : void
	{
		// Usage data from before November 2014 does not currently exist.
		if ($month->format('Y-m') < '2014-11') {
			$m = $month->format('Y-m-d');
			throw new InvalidMonthException("This month is too old: $m.");
		}

		// Usage data from the future does not currently exist.
		$today = new DateTime('today');
		if ($month > $today) {
			$m = $month->format('Y-m-d');
			throw new InvalidMonthException("This month has not happened yet: $m.");
		}
	}
}
