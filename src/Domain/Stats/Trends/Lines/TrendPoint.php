<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Trends\Lines;

use DateTime;

final class TrendPoint
{
	public function __construct(
		private DateTime $date,
		private float $value,
	) {}

	/**
	 * Get the trend point's date.
	 *
	 * @return DateTime
	 */
	public function getDate() : DateTime
	{
		return $this->date;
	}

	/**
	 * Get the trend point's value.
	 *
	 * @return float
	 */
	public function getValue() : float
	{
		return $this->value;
	}
}
