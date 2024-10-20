<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Usage;

final readonly class StatsMovePokemon
{
	public function __construct(
		private string $icon,
		private string $identifier,
		private string $name,
		private float $pokemonPercent,
		private float $movePercent,
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

	public function getMovePercent() : float
	{
		return $this->movePercent;
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
