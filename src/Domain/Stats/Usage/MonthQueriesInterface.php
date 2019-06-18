<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Usage;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;

interface MonthQueriesInterface
{
	/**
	 * Get the previous month with usage data for any format.
	 *
	 * @param DateTime $month
	 *
	 * @return DateTime|null
	 */
	public function getPrev(DateTime $month) : ?DateTime;

	/**
	 * Get the next month with usage data for any format.
	 *
	 * @param DateTime $month
	 *
	 * @return DateTime|null
	 */
	public function getNext(DateTime $month) : ?DateTime;

	/**
	 * Get the previous month with usage data for this format.
	 *
	 * @param DateTime $month
	 * @param FormatId $formatId
	 *
	 * @return DateTime|null
	 */
	public function getPrevByFormat(DateTime $month, FormatId $formatId) : ?DateTime;

	/**
	 * Get the next month with usage data for this format.
	 *
	 * @param DateTime $month
	 * @param FormatId $formatId
	 *
	 * @return DateTime|null
	 */
	public function getNextByFormat(DateTime $month, FormatId $formatId) : ?DateTime;
}
