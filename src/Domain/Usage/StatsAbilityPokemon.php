<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Usage;

final readonly class StatsAbilityPokemon
{
	public function __construct(
		private string $icon,
		private string $identifier,
		private string $name,
		private float $pokemonPercent,
		private float $abilityPercent,
		private float $usagePercent,
		private float $usageChange,
		private int $baseSpeed,
	) {}

	public function getIcon() : string
	{
		return $this->icon;
	}

	public function getIdentifier() : string
	{
		return $this->identifier;
	}

	public function getName() : string
	{
		return $this->name;
	}

	public function getPokemonPercent() : float
	{
		return $this->pokemonPercent;
	}

	public function getAbilityPercent() : float
	{
		return $this->abilityPercent;
	}

	public function getUsagePercent() : float
	{
		return $this->usagePercent;
	}

	public function getUsageChange() : float
	{
		return $this->usageChange;
	}

	public function getBaseSpeed() : int
	{
		return $this->baseSpeed;
	}
}
