<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use Jp\Dex\Domain\YearMonth;

class DateModel
{
	/** @var DateHelper $dateHelper */
	private $dateHelper;

	/** @var YearMonth $thisMonth */
	private $thisMonth;

	/** @var YearMonth $prevMonth */
	private $prevMonth;

	/** @var YearMonth $nextMonth */
	private $nextMonth;

	/**
	 * Constructor.
	 *
	 * @param DateHelper $dateHelper
	 */
	public function __construct(DateHelper $dateHelper)
	{
		$this->dateHelper = $dateHelper;
	}

	/**
	 * Set the previous month and the next month, calculated from the given year
	 * and month combination.
	 *
	 * @param int $year
	 * @param int $month
	 *
	 * @return void
	 */
	public function setData(int $year, int $month) : void
	{
		// Calculate the previous month and the next month.
		$this->thisMonth = new YearMonth($year, $month);
		$this->prevMonth = $this->dateHelper->getPreviousMonth($this->thisMonth);
		$this->nextMonth = $this->dateHelper->getNextMonth($this->thisMonth);
	}

	/**
	 * Get the current month.
	 *
	 * @return YearMonth
	 */
	public function getThisMonth() : YearMonth
	{
		return $this->thisMonth;
	}

	/**
	 * Get the previous month.
	 *
	 * @return YearMonth
	 */
	public function getPrevMonth() : YearMonth
	{
		return $this->prevMonth;
	}

	/**
	 * Get the next month.
	 *
	 * @return YearMonth
	 */
	public function getNextMonth() : YearMonth
	{
		return $this->nextMonth;
	}
}
