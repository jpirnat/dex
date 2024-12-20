<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Stats\Usage\MonthQueriesInterface;

final class DateModel
{
	private(set) DateTime $thisMonth;
	private(set) ?DateTime $prevMonth;
	private(set) ?DateTime $nextMonth;


	public function __construct(
		private readonly MonthQueriesInterface $monthQueries,
	) {}


	/**
	 * Set this month, prev month, and next month based on the given month.
	 *
	 * @param string $month "YYYY-MM"
	 */
	public function setMonth(string $month) : void
	{
		$this->thisMonth = new DateTime("$month-01");

		$this->prevMonth = $this->monthQueries->getPrev($this->thisMonth);
		$this->nextMonth = $this->monthQueries->getNext($this->thisMonth);
	}

	/**
	 * Set this month, prev month, and next month based on the given month and format.
	 *
	 * @param string $month "YYYY-MM"
	 */
	public function setMonthAndFormat(string $month, FormatId $formatId) : void
	{
		$this->thisMonth = new DateTime("$month-01");

		$this->prevMonth = $this->monthQueries->getPrevByFormat($this->thisMonth, $formatId);
		$this->nextMonth = $this->monthQueries->getNextByFormat($this->thisMonth, $formatId);
	}
}
