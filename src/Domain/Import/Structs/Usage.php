<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Import\Structs;

final class Usage
{
	public function __construct(
		private int $rank,
		private string $showdownPokemonName,
		private float $usagePercent,
		private int $raw,
		private float $rawPercent,
		private int $real,
		private float $realPercent,
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

		// Clamp real percent between 0 and 100.
		if ($this->realPercent < 0) {
			$this->realPercent = 0;
		}
		if ($this->realPercent > 100) {
			$this->realPercent = 100;
		}
	}

	/**
	 * Get the rank.
	 *
	 * @return int
	 */
	public function rank() : int
	{
		return $this->rank;
	}

	/**
	 * Get the Pokémon Showdown Pokémon name.
	 *
	 * @return string
	 */
	public function showdownPokemonName() : string
	{
		return $this->showdownPokemonName;
	}

	/**
	 * Get the usage percent.
	 *
	 * @return float
	 */
	public function usagePercent() : float
	{
		return $this->usagePercent;
	}

	/**
	 * Get the raw count.
	 *
	 * @return int
	 */
	public function raw() : int
	{
		return $this->raw;
	}

	/**
	 * Get the raw percent.
	 *
	 * @return float
	 */
	public function rawPercent() : float
	{
		return $this->rawPercent;
	}

	/**
	 * Get the real count.
	 *
	 * @return int
	 */
	public function real() : int
	{
		return $this->real;
	}

	/**
	 * Get the real percent.
	 *
	 * @return float
	 */
	public function realPercent() : float
	{
		return $this->realPercent;
	}
}
