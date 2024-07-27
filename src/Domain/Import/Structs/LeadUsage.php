<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Import\Structs;

final readonly class LeadUsage
{
	private float $usagePercent;
	private float $rawPercent;

	public function __construct(
		private int $rank,
		private string $showdownPokemonName,
		float $usagePercent,
		private int $raw,
		float $rawPercent,
	) {
		// Clamp usage percent between 0 and 100.
		if ($usagePercent < 0) {
			$usagePercent = 0;
		}
		if ($usagePercent > 100) {
			$usagePercent = 100;
		}
		$this->usagePercent = $usagePercent;

		// Clamp raw percent between 0 and 100.
		if ($rawPercent < 0) {
			$rawPercent = 0;
		}
		if ($rawPercent > 100) {
			$rawPercent = 100;
		}
		$this->rawPercent = $rawPercent;
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
