<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Trends;

use DateTime;

class TrendPoint
{
	/** @var DateTime $date */
	private $date;

	/** @var float $value */
	private $value;

	/**
	 * Constructor.
	 *
	 * @param DateTime $date
	 * @param float $value
	 */
	public function __construct(DateTime $date, float $value)
	{
		$this->date = $date;
		$this->value = $value;
	}

	/**
	 * Get the date.
	 *
	 * @return DateTime
	 */
	public function getDate() : DateTime
	{
		return $this->date;
	}

	/**
	 * Get the value.
	 *
	 * @return float
	 */
	public function getValue() : float
	{
		return $this->value;
	}
}
