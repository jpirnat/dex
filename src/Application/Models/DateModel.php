<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Stats\Usage\MonthQueriesInterface;

class DateModel
{
	/** @var MonthQueriesInterface $monthQueries */
	private $monthQueries;


	/** @var DateTime $thisMonth */
	private $thisMonth;

	/** @var DateTime $prevMonth */
	private $prevMonth;

	/** @var DateTime $nextMonth */
	private $nextMonth;


	/**
	 * Constructor.
	 *
	 * @param MonthQueriesInterface $monthQueries
	 */
	public function __construct(MonthQueriesInterface $monthQueries)
	{
		$this->monthQueries = $monthQueries;
	}


	/**
	 * Set this month, prev month, and next month based on the given month.
	 *
	 * @param string $month "YYYY-MM"
	 *
	 * @return void
	 */
	public function setMonth(string $month) : void
	{
		$this->thisMonth = new DateTime("$month-01");

		$this->prevMonth = $this->monthQueries->getPrev($this->thisMonth);
		$this->nextMonth = $this->monthQueries->getNext($this->thisMonth);
	}

	/**
	 * Set this month, prev month, and next month based on the given month and
	 * format.
	 *
	 * @param string $month "YYYY-MM"
	 * @param FormatId $formatId
	 *
	 * @return void
	 */
	public function setMonthAndFormat(string $month, FormatId $formatId) : void
	{
		$this->thisMonth = new DateTime("$month-01");

		$this->prevMonth = $this->monthQueries->getPrevByFormat($this->thisMonth, $formatId);
		$this->nextMonth = $this->monthQueries->getNextByFormat($this->thisMonth, $formatId);
	}


	/**
	 * Get the current month.
	 *
	 * @return DateTime
	 */
	public function getThisMonth() : DateTime
	{
		return $this->thisMonth;
	}

	/**
	 * Get the previous month.
	 *
	 * @return DateTime|null
	 */
	public function getPrevMonth() : ?DateTime
	{
		return $this->prevMonth;
	}

	/**
	 * Get the next month.
	 *
	 * @return DateTime|null
	 */
	public function getNextMonth() : ?DateTime
	{
		return $this->nextMonth;
	}
}
