<?php
declare(strict_types=1);

namespace Jp\Dex\Stats\Importers\Structs;

class Usage
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

	/** @var int $real */
	protected $real;

	/** @var float $realPercent */
	protected $realPercent;

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
