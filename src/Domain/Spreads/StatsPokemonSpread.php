<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Spreads;

use Jp\Dex\Domain\Stats\StatId;
use Jp\Dex\Domain\Stats\StatValueContainer;

final class StatsPokemonSpread
{
	public function __construct(
		private string $natureName,
		private ?StatId $increasedStatId,
		private ?StatId $decreasedStatId,
		private StatValueContainer $evs,
		private float $percent,
	) {}

	/**
	 * Get the spread's nature name.
	 *
	 * @return string
	 */
	public function getNatureName() : string
	{
		return $this->natureName;
	}

	/**
	 * Get the spread's nature-increased stat id.
	 *
	 * @return StatId|null
	 */
	public function getIncreasedStatId() : ?StatId
	{
		return $this->increasedStatId;
	}

	/**
	 * Get the spread's nature-decreased stat id.
	 *
	 * @return StatId|null
	 */
	public function getDecreasedStatId() : ?StatId
	{
		return $this->decreasedStatId;
	}

	/**
	 * Get the spread's EVs.
	 *
	 * @return StatValueContainer
	 */
	public function getEvs() : StatValueContainer
	{
		return $this->evs;
	}

	/**
	 * Get the spread's percent.
	 *
	 * @return float
	 */
	public function getPercent() : float
	{
		return $this->percent;
	}
}
