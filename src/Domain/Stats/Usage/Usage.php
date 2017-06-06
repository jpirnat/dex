<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Usage;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Stats\Exceptions\InvalidCountException;
use Jp\Dex\Domain\Stats\Exceptions\InvalidMonthException;
use Jp\Dex\Domain\Stats\Exceptions\InvalidYearException;

class Usage
{
	/** @var int $year */
	private $year;

	/** @var int $month */
	private $month;

	/** @var FormatId $formatId */
	private $formatId;

	/** @var int $totalBattles */
	private $totalBattles;

	/**
	 * Constructor.
	 *
	 * @param int $year
	 * @param int $month
	 * @param FormatId $formatId
	 * @param int $totalBattles
	 *
	 * @throws InvalidYearException if $year is invalid.
	 * @throws InvalidMonthException if $month is invalid.
	 * @throws InvalidCountException if $totalBattles is invalid.
	 */
	public function __construct(
		int $year,
		int $month,
		FormatId $formatId,
		int $totalBattles
	) {
		$today = new DateTime();
		$currentYear = (int) $today->format('Y');
		$currentMonth = (int) $today->format('n');

		if ($year < 2014) {
			throw new InvalidYearException('Invalid year: ' . $year);
		}

		if ($year > $currentYear) {
			throw new InvalidYearException(
				'This year has not happened yet: ' . $year
			);
		}

		if ($month < 1 || $month > 12) {
			throw new InvalidMonthException('Invalid month: ' . $month);
		}

		if ($year === $currentYear && $month > $currentMonth) {
			throw new InvalidMonthException(
				'This month has not happened yet: ' . $month
			);
		}

		if ($totalBattles < 0) {
			throw new InvalidCountException(
				'Invalid number of total battles: ' . $totalBattles
			);
		}

		$this->year = $year;
		$this->month = $month;
		$this->formatId = $formatId;
		$this->totalBattles = $totalBattles;
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

	/**
	 * Get the format id.
	 *
	 * @return FormatId
	 */
	public function getFormatId() : FormatId
	{
		return $this->formatId;
	}

	/**
	 * Get the total battles.
	 *
	 * @return int
	 */
	public function getTotalBattles() : int
	{
		return $this->totalBattles;
	}
}
