<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use DateTime;

class DateModel
{
	/** @var DateTime $thisMonth */
	private $thisMonth;

	/** @var DateTime $prevMonth */
	private $prevMonth;

	/** @var DateTime $nextMonth */
	private $nextMonth;

	/**
	 * Set this month, the previous month, and the next month, calculated from
	 * the given year and month combination.
	 *
	 * @param string $month "YYYY-MM"
	 *
	 * @return void
	 */
	public function setData(string $month) : void
	{
		$this->thisMonth = new DateTime("$month-01");

		$this->prevMonth = clone $this->thisMonth;
		$this->prevMonth->modify('-1 month');

		$this->nextMonth = clone $this->thisMonth;
		$this->nextMonth->modify('+1 month');
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
	 * @return DateTime
	 */
	public function getPrevMonth() : DateTime
	{
		return $this->prevMonth;
	}

	/**
	 * Get the next month.
	 *
	 * @return DateTime
	 */
	public function getNextMonth() : DateTime
	{
		return $this->nextMonth;
	}
}
