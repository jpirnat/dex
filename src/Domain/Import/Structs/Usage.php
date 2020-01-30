<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Import\Structs;

final class Usage
{
	private int $rank;
	private string $showdownPokemonName;
	private float $usagePercent;
	private int $raw;
	private float $rawPercent;
	private int $real;
	private float $realPercent;

	/**
	 * Constructor.
	 *
	 * @param int $rank
	 * @param string $showdownPokemonName
	 * @param float $usagePercent
	 * @param int $raw
	 * @param float $rawPercent
	 * @param int $real
	 * @param float $realPercent
	 */
	public function __construct(
		int $rank,
		string $showdownPokemonName,
		float $usagePercent,
		int $raw,
		float $rawPercent,
		int $real,
		float $realPercent
	) {
		// Clamp usage percent between 0 and 100.
		if ($usagePercent < 0) {
			$usagePercent = 0;
		}
		if ($usagePercent > 100) {
			$usagePercent = 100;
		}

		// Clamp raw percent between 0 and 100.
		if ($rawPercent < 0) {
			$rawPercent = 0;
		}
		if ($rawPercent > 100) {
			$rawPercent = 100;
		}

		// Clamp real percent between 0 and 100.
		if ($realPercent < 0) {
			$realPercent = 0;
		}
		if ($realPercent > 100) {
			$realPercent = 100;
		}

		$this->rank = $rank;
		$this->showdownPokemonName = $showdownPokemonName;
		$this->usagePercent = $usagePercent;
		$this->raw = $raw;
		$this->rawPercent = $rawPercent;
		$this->real = $real;
		$this->realPercent = $realPercent;
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
