<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Usage;

interface UsageRatedQueriesInterface
{
	/**
	 * Get the format/rating combinations for this year and month.
	 *
	 * @param int $year
	 * @param int $month
	 *
	 * @return array An array of the form [
	 *     [
	 *         'formatId' => FormatId
	 *         'rating' => int
	 *     ],
	 *     ...
	 * ]
	 */
	public function getFormatRatings(int $year, int $month) : array;
}
