<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\StatsAveragedPokemon;

final readonly class AbilityData
{
	public function __construct(
		private string $abilityName,
		private string $abilityIdentifier,
		private float $percent,
	) {}

	/**
	 * Get the ability name.
	 */
	public function getAbilityName() : string
	{
		return $this->abilityName;
	}

	/**
	 * Get the ability identifier.
	 */
	public function getAbilityIdentifier() : string
	{
		return $this->abilityIdentifier;
	}

	/**
	 * Get the percent.
	 */
	public function getPercent() : float
	{
		return $this->percent;
	}
}
