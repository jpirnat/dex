<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Usage;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;

interface UsageQueriesInterface
{
	/**
	 * Get the months that have usage records.
	 *
	 * @return DateTime[]
	 */
	public function getMonths() : array;

	/**
	 * Get the month of the oldest instance of data in this format.
	 *
	 * @param FormatId $formatId
	 *
	 * @return DateTime|null
	 */
	public function getOldest(FormatId $formatId) : ?DateTime;

	/**
	 * Get the month of the newest instance of data in this format.
	 *
	 * @param FormatId $formatId
	 *
	 * @return DateTime|null
	 */
	public function getNewest(FormatId $formatId) : ?DateTime;
}
