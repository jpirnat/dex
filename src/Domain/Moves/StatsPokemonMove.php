<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Moves;

final readonly class StatsPokemonMove
{
	public function __construct(
		private string $identifier,
		private string $name,
		private float $percent,
		private float $change,
	) {}

	/**
	 * Get the move's identifier.
	 */
	public function getIdentifier() : string
	{
		return $this->identifier;
	}

	/**
	 * Get the move's name.
	 */
	public function getName() : string
	{
		return $this->name;
	}

	/**
	 * Get the move's percent.
	 */
	public function getPercent() : float
	{
		return $this->percent;
	}

	/**
	 * Get the move's change.
	 */
	public function getChange() : float
	{
		return $this->change;
	}
}
