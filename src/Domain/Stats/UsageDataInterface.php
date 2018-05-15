<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats;

use DateTime;

interface UsageDataInterface
{
	/**
	 * Get the month.
	 *
	 * @return DateTime
	 */
	public function getMonth() : DateTime;
}
