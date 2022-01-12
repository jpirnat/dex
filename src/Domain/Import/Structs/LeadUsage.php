<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Import\Structs;

final class LeadUsage
{
	public function __construct(
		private int $rank,
		private string $showdownPokemonName,
		private float $usagePercent,
		private int $raw,
		private float $rawPercent,
	) {
		// Clamp usage percent between 0 and 100.
		if ($this->usagePercent < 0) {
			$this->usagePercent = 0;
		}
		if ($this->usagePercent > 100) {
			$this->usagePercent = 100;
		}

		// Clamp raw percent between 0 and 100.
		if ($this->rawPercent < 0) {
			$this->rawPercent = 0;
		}
		if ($this->rawPercent > 100) {
			$this->rawPercent = 100;
		}
	}

	/**
	 * Get the rank.
	 */
	public function rank() : int
	{
		return $this->rank;
	}

	/**
	 * Get the Pokémon Showdown Pokémon name.
	 */
	public function showdownPokemonName() : string
	{
		return $this->showdownPokemonName;
	}

	/**
	 * Get the usage percent.
	 */
	public function usagePercent() : float
	{
		return $this->usagePercent;
	}

	/**
	 * Get the raw count.
	 */
	public function raw() : int
	{
		return $this->raw;
	}

	/**
	 * Get the raw percent.
	 */
	public function rawPercent() : float
	{
		return $this->rawPercent;
	}
}
