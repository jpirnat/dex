<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\StatsPokemon;

final class StatData
{
	public function __construct(
		private string $statName,
		private int $baseStat,
	) {}

	/**
	 * Get the stat name.
	 *
	 * @return string
	 */
	public function getStatName() : string
	{
		return $this->statName;
	}

	/**
	 * Get the base stat.
	 *
	 * @return int
	 */
	public function getBaseStat() : int
	{
		return $this->baseStat;
	}
}
