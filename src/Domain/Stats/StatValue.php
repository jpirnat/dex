<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats;

final class StatValue
{
	public function __construct(
		private StatId $statId,
		private float $value,
	) {}

	/**
	 * Get the stat id.
	 *
	 * @return StatId
	 */
	public function getStatId() : StatId
	{
		return $this->statId;
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
