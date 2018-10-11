<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Usage;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;

interface MonthQueriesInterface
{
	/**
	 * Does usage data exist for this month?
	 *
	 * @param DateTime $month
	 *
	 * @return bool
	 */
	public function doesMonthDataExist(DateTime $month) : bool;

	/**
	 * Does usage data exist for this month and format?
	 *
	 * @param DateTime $month
	 * @param FormatId $formatId
	 *
	 * @return bool
	 */
	public function doesMonthFormatDataExist(DateTime $month, FormatId $formatId) : bool;
}
