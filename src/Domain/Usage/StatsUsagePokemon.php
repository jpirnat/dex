<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Usage;

final readonly class StatsUsagePokemon
{
	public function __construct(
		private int $rank,
		private string $icon,
		private string $identifier,
		private string $name,
		private float $usagePercent,
		private float $usageChange,
		private int $raw,
		private float $rawPercent,
		private int $real,
		private float $realPercent,
		private int $baseSpeed,
	) {}

	public function getRank() : int
	{
		return $this->rank;
	}

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

	public function getUsagePercent() : float
	{
		return $this->usagePercent;
	}

	public function getUsageChange() : float
	{
		return $this->usageChange;
	}

	public function getRaw() : int
	{
		return $this->raw;
	}

	public function getRawPercent() : float
	{
		return $this->rawPercent;
	}

	public function getReal() : int
	{
		return $this->real;
	}

	public function getRealPercent() : float
	{
		return $this->realPercent;
	}

	public function getBaseSpeed() : int
	{
		return $this->baseSpeed;
	}
}
