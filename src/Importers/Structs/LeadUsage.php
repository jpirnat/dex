<?php
declare(strict_types=1);

namespace Jp\Trendalyzer\Importers\Structs;

class LeadUsage
{
	/** @var int $rank */
	protected $rank;

	/** @var string $pokemonName */
	protected $pokemonName;

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
	 * @param string $pokemonName
	 * @param float $usagePercent
	 * @param int $raw
	 * @param float $rawPercent
	 */
	public function __construct(
		int $rank,
		string $pokemonName,
		float $usagePercent,
		int $raw,
		float $rawPercent
	) {
		$this->rank = $rank;
		$this->pokemonName = $pokemonName;
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
	 * Get the Pokémon's name.
	 *
	 * @return string
	 */
	public function pokemonName() : string
	{
		return $this->pokemonName;
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
