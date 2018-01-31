<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use DateTime;
use IntlDateFormatter;
use Jp\Dex\Domain\YearMonth;
use NumberFormatter;

class IntlFormatter
{
	/** @var IntlDateFormatter $dateFormatter */
	private $dateFormatter;

	/** @var NumberFormatter $numberFormatter */
	private $numberFormatter;

	/**
	 * Constructor.
	 *
	 * @param IntlDateFormatter $dateFormatter
	 * @param NumberFormatter $numberFormatter
	 */
	public function __construct(
		IntlDateFormatter $dateFormatter,
		NumberFormatter $numberFormatter
	) {
		$this->dateFormatter = $dateFormatter;
		$this->numberFormatter = $numberFormatter;
	}

	/**
	 * Format a year/month.
	 *
	 * @param YearMonth $yearMonth
	 *
	 * @return string
	 */
	public function formatYearMonth(YearMonth $yearMonth) : string
	{
		$date = new DateTime();
		$date->setDate($yearMonth->getYear(), $yearMonth->getMonth(), 1);
		return mb_convert_case($this->dateFormatter->format($date), MB_CASE_TITLE);
	}

	/**
	 * Format a number.
	 *
	 * @param float $number
	 *
	 * @return string
	 */
	public function formatNumber(float $number) : string
	{
		return $this->numberFormatter->format($number);
	}
}
