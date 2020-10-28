<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Usage;

final class StatsUsagePokemon
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
	) {}

	/**
	 * Get the Pokémon's rank.
	 *
	 * @return int
	 */
	public function getRank() : int
	{
		return $this->rank;
	}

	/**
	 * Get the Pokémon's icon.
	 *
	 * @return string
	 */
	public function getIcon() : string
	{
		return $this->icon;
	}

	/**
	 * Get the Pokémon's identifier.
	 *
	 * @return string
	 */
	public function getIdentifier() : string
	{
		return $this->identifier;
	}

	/**
	 * Get the Pokémon's name.
	 *
	 * @return string
	 */
	public function getName() : string
	{
		return $this->name;
	}

	/**
	 * Get the Pokémon's usage percent.
	 *
	 * @return float
	 */
	public function getUsagePercent() : float
	{
		return $this->usagePercent;
	}

	/**
	 * Get the Pokémon's usage change.
	 *
	 * @return float
	 */
	public function getUsageChange() : float
	{
		return $this->usageChange;
	}

	/**
	 * Get the Pokémon's raw count.
	 *
	 * @return int
	 */
	public function getRaw() : int
	{
		return $this->raw;
	}

	/**
	 * Get the Pokémon's raw percent.
	 *
	 * @return float
	 */
	public function getRawPercent() : float
	{
		return $this->rawPercent;
	}

	/**
	 * Get the Pokémon's real count.
	 *
	 * @return int
	 */
	public function getReal() : int
	{
		return $this->real;
	}

	/**
	 * Get the Pokémon's real percent.
	 *
	 * @return float
	 */
	public function getRealPercent() : float
	{
		return $this->realPercent;
	}
}
