<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Import\Structs;

class YearMonth
{
	/** @var int $year */
	protected $year;

	/** @var int $month */
	protected $month;

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
	public function year() : int
	{
		return $this->year;
	}

	/**
	 * Get the month.
	 *
	 * @return int
	 */
	public function month() : int
	{
		return $this->month;
	}
}
