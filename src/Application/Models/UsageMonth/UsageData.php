<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\UsageMonth;

class UsageData
{
	/** @var int $rank */
	private $rank;

	/** @var string $pokemonName */
	private $pokemonName;

	/** @var string $pokemonIdentifier */
	private $pokemonIdentifier;

	/** @var float $usagePercent */
	private $usagePercent;

	/** @var float $usageChange */
	private $usageChange;

	/** @var int $raw */
	private $raw;

	/** @var float $rawPercent */
	private $rawPercent;

	/** @var float $rawChange */
	private $rawChange;

	/** @var int $real */
	private $real;

	/** @var float $realPercent */
	private $realPercent;

	/** @var float $realChange */
	private $realChange;

	/**
	 * Constructor.
	 *
	 * @param int $rank
	 * @param string $pokemonName
	 * @param string $pokemonIdentifier
	 * @param float $usagePercent
	 * @param float $usageChange
	 * @param int $raw
	 * @param float $rawPercent
	 * @param float $rawChange
	 * @param int $real
	 * @param float $realPercent
	 * @param float $realChange
	 */
	public function __construct(
		int $rank,
		string $pokemonName,
		string $pokemonIdentifier,
		float $usagePercent,
		float $usageChange,
		int $raw,
		float $rawPercent,
		float $rawChange,
		int $real,
		float $realPercent,
		float $realChange
	) {
		$this->rank = $rank;
		$this->pokemonName = $pokemonName;
		$this->pokemonIdentifier = $pokemonIdentifier;
		$this->usagePercent = $usagePercent;
		$this->usageChange = $usageChange;
		$this->raw = $raw;
		$this->rawPercent = $rawPercent;
		$this->rawChange = $rawChange;
		$this->real = $real;
		$this->realPercent = $realPercent;
		$this->realChange = $realChange;
	}

	/**
	 * Get the rank.
	 *
	 * @return int
	 */
	public function getRank() : int
	{
		return $this->rank;
	}

	/**
	 * Get the Pokémon name.
	 *
	 * @return string
	 */
	public function getPokemonName() : string
	{
		return $this->pokemonName;
	}

	/**
	 * Get the Pokémon identifier.
	 *
	 * @return string
	 */
	public function getPokemonIdentifier() : string
	{
		return $this->pokemonIdentifier;
	}

	/**
	 * Get the usage percent.
	 *
	 * @return float
	 */
	public function getUsagePercent() : float
	{
		return $this->usagePercent;
	}

	/**
	 * Get the usage change.
	 *
	 * @return float
	 */
	public function getUsageChange() : float
	{
		return $this->usageChange;
	}

	/**
	 * Get the raw.
	 *
	 * @return int
	 */
	public function getRaw() : int
	{
		return $this->raw;
	}

	/**
	 * Get the raw percent.
	 *
	 * @return float
	 */
	public function getRawPercent() : float
	{
		return $this->rawPercent;
	}

	/**
	 * Get the raw change.
	 *
	 * @return float
	 */
	public function getRawChange() : float
	{
		return $this->rawChange;
	}

	/**
	 * Get the real.
	 *
	 * @return int
	 */
	public function getReal() : int
	{
		return $this->real;
	}

	/**
	 * Get the real percent.
	 *
	 * @return float
	 */
	public function getRealPercent() : float
	{
		return $this->realPercent;
	}

	/**
	 * Get the real change.
	 *
	 * @return float
	 */
	public function getRealChange() : float
	{
		return $this->realChange;
	}
}
