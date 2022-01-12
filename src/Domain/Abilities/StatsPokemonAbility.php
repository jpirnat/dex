<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Abilities;

final class StatsPokemonAbility
{
	public function __construct(
		private string $identifier,
		private string $name,
		private float $percent,
		private float $change,
	) {}

	/**
	 * Get the ability's identifier.
	 */
	public function getIdentifier() : string
	{
		return $this->identifier;
	}

	/**
	 * Get the ability's name.
	 */
	public function getName() : string
	{
		return $this->name;
	}

	/**
	 * Get the ability's percent.
	 */
	public function getPercent() : float
	{
		return $this->percent;
	}

	/**
	 * Get the ability's change.
	 */
	public function getChange() : float
	{
		return $this->change;
	}
}
