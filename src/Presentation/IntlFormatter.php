<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use DateTime;
use IntlDateFormatter;
use NumberFormatter;

final class IntlFormatter
{
	public function __construct(
		private IntlDateFormatter $dateFormatter,
		private NumberFormatter $numberFormatter,
		private NumberFormatter $percentFormatter,
		private NumberFormatter $changeFormatter,
	) {}

	/**
	 * Format a month.
	 *
	 * @param DateTime $month
	 *
	 * @return string
	 */
	public function formatMonth(DateTime $month) : string
	{
		return mb_convert_case($this->dateFormatter->format($month), MB_CASE_TITLE);
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

	/**
	 * Format a percent.
	 *
	 * @param float $percent A number between 0 and 100.
	 *
	 * @return string
	 */
	public function formatPercent(float $percent) : string
	{
		return $this->percentFormatter->format($percent / 100);
	}

	/**
	 * Format a percent.
	 *
	 * @param float $percent A number between 0 and 100.
	 *
	 * @return string
	 */
	public function formatChange(float $percent) : string
	{
		return $this->changeFormatter->format($percent / 100);
	}
}
