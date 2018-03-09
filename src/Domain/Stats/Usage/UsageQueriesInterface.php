<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Usage;

use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\YearMonth;

interface UsageQueriesInterface
{
	/**
	 * Get the year/month combinations that have usage records.
	 *
	 * @return YearMonth[]
	 */
	public function getYearMonths() : array;

	/**
	 * Get the year/month of the oldest instance of data in this format.
	 *
	 * @param FormatId $formatId
	 *
	 * @return YearMonth|null
	 */
	public function getOldest(FormatId $formatId) : ?YearMonth;

	/**
	 * Get the year/month of the newest instance of data in this format.
	 *
	 * @param FormatId $formatId
	 *
	 * @return YearMonth|null
	 */
	public function getNewest(FormatId $formatId) : ?YearMonth;
}
