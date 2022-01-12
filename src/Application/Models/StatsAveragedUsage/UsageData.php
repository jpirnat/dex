<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\StatsAveragedUsage;

final class UsageData
{
	public function __construct(
		private int $rank,
		private string $pokemonName,
		private int $months,
		private string $pokemonIdentifier,
		private string $formIcon,
		private float $usagePercent,
		private int $raw,
		private float $rawPercent,
		private int $real,
		private float $realPercent,
	) {}

	/**
	 * Get the rank.
	 */
	public function getRank() : int
	{
		return $this->rank;
	}

	/**
	 * Get the Pokémon name.
	 */
	public function getPokemonName() : string
	{
		return $this->pokemonName;
	}

	/**
	 * Get the months.
	 */
	public function getMonths() : int
	{
		return $this->months;
	}

	/**
	 * Get the Pokémon identifier.
	 */
	public function getPokemonIdentifier() : string
	{
		return $this->pokemonIdentifier;
	}

	/**
	 * Get the form icon.
	 */
	public function getFormIcon() : string
	{
		return $this->formIcon;
	}

	/**
	 * Get the usage percent.
	 */
	public function getUsagePercent() : float
	{
		return $this->usagePercent;
	}

	/**
	 * Get the raw.
	 */
	public function getRaw() : int
	{
		return $this->raw;
	}

	/**
	 * Get the raw percent.
	 */
	public function getRawPercent() : float
	{
		return $this->rawPercent;
	}

	/**
	 * Get the real.
	 */
	public function getReal() : int
	{
		return $this->real;
	}

	/**
	 * Get the real percent.
	 */
	public function getRealPercent() : float
	{
		return $this->realPercent;
	}
}
