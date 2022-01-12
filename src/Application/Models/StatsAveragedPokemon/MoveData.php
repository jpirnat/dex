<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\StatsAveragedPokemon;

final class MoveData
{
	public function __construct(
		private string $moveName,
		private string $moveIdentifier,
		private float $percent,
	) {}

	/**
	 * Get the move name.
	 */
	public function getMoveName() : string
	{
		return $this->moveName;
	}

	/**
	 * Get the move identifier.
	 */
	public function getMoveIdentifier() : string
	{
		return $this->moveIdentifier;
	}

	/**
	 * Get the percent.
	 */
	public function getPercent() : float
	{
		return $this->percent;
	}
}
