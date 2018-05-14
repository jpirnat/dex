<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Usage;

use DateTime;

interface UsageRatedQueriesInterface
{
	/**
	 * Get the format/rating combinations for this month.
	 *
	 * @param DateTime $month
	 *
	 * @return array An array of the form [
	 *     [
	 *         'formatId' => FormatId
	 *         'rating' => int
	 *     ],
	 *     ...
	 * ]
	 */
	public function getFormatRatings(DateTime $month) : array;
}
