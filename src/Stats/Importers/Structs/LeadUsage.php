<?php
declare(strict_types=1);

namespace Jp\Dex\Stats\Importers\Structs;

class LeadUsage
{
	/** @var int $rank */
	protected $rank;

	/** @var string $showdownPokemonName */
	protected $showdownPokemonName;

	/** @var float $usagePercent */
	protected $usagePercent;

	/** @var int $raw */
	protected $raw;

	/** @var float $rawPercent */
	protected $rawPercent;

	/**
	 * Constructor.
	 *
	 * @param int $rank
	 * @param string $showdownPokemonName
	 * @param float $usagePercent
	 * @param int $raw
	 * @param float $rawPercent
	 */
	public function __construct(
		int $rank,
		string $showdownPokemonName,
		float $usagePercent,
		int $raw,
		float $rawPercent
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

		$this->rank = $rank;
		$this->showdownPokemonName = $showdownPokemonName;
		$this->usagePercent = $usagePercent;
		$this->raw = $raw;
		$this->rawPercent = $rawPercent;
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
}
