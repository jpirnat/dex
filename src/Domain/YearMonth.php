<?php
declare(strict_types=1);

namespace Jp\Dex\Domain;

class YearMonth
{
	/** @var int $year */
	private $year;

	/** @var int $month */
	private $month;

	/**
	 * Constructor.
	 *
	 * @param int $year
	 * @param int $month
	 */
	public function __construct(int $year, int $month)
	{
		$this->year = $year;
		$this->month = $month;
	}

	/**
	 * Get the year.
	 *
	 * @return int
	 */
	public function getYear() : int
	{
		return $this->year;
	}

	/**
	 * Get the month.
	 *
	 * @return int
	 */
	public function getMonth() : int
	{
		return $this->month;
	}
}
