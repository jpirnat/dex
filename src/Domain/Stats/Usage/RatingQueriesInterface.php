<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Usage;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;

interface RatingQueriesInterface
{
	/**
	 * Get the ratings for which usage data is available for this month and format.
	 *
	 * @return int[]
	 */
	public function getByMonthAndFormat(DateTime $month, FormatId $formatId) : array;

	/**
	 * Get the ratings for which usage data is available between these months, for this format.
	 *
	 * @return int[]
	 */
	public function getByMonthsAndFormat(DateTime $start, DateTime $end, FormatId $formatId) : array;
}
