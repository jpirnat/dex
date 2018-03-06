<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats;

interface UsageDataInterface
{
	/**
	 * Get the year.
	 *
	 * @return int
	 */
	public function getYear() : int;

	/**
	 * Get the month.
	 *
	 * @return int
	 */
	public function getMonth() : int;
}
