<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Trends\Lines;

use DateTime;

final readonly class TrendPoint
{
	public function __construct(
		private DateTime $date,
		private float $value,
	) {}

	/**
	 * Get the trend point's date.
	 */
	public function getDate() : DateTime
	{
		return $this->date;
	}

	/**
	 * Get the trend point's value.
	 */
	public function getValue() : float
	{
		return $this->value;
	}
}
