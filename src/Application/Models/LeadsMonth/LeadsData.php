<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\LeadsMonth;

class LeadsData
{
	/** @var int $rank */
	private $rank;

	/** @var string $pokemonName */
	private $pokemonName;

	/** @var float $usagePercent */
	private $usagePercent;

	/** @var string $pokemonIdentifier */
	private $pokemonIdentifier;

	/** @var string $formIcon */
	private $formIcon;

	/** @var float $leadUsagePercent */
	private $leadUsagePercent;

	/** @var float $leadUsageChange */
	private $leadUsageChange;

	/** @var int $raw */
	private $raw;

	/** @var float $rawPercent */
	private $rawPercent;

	/** @var float $rawChange */
	private $rawChange;

	/**
	 * Constructor.
	 *
	 * @param int $rank
	 * @param string $pokemonName
	 * @param float $usagePercent
	 * @param string $pokemonIdentifier
	 * @param string $formIcon
	 * @param float $leadUsagePercent
	 * @param float $leadUsageChange
	 * @param int $raw
	 * @param float $rawPercent
	 * @param float $rawChange
	 */
	public function __construct(
		int $rank,
		string $pokemonName,
		float $usagePercent,
		string $pokemonIdentifier,
		string $formIcon,
		float $leadUsagePercent,
		float $leadUsageChange,
		int $raw,
		float $rawPercent,
		float $rawChange
	) {
		$this->rank = $rank;
		$this->pokemonName = $pokemonName;
		$this->usagePercent = $usagePercent;
		$this->pokemonIdentifier = $pokemonIdentifier;
		$this->formIcon = $formIcon;
		$this->leadUsagePercent = $leadUsagePercent;
		$this->leadUsageChange = $leadUsageChange;
		$this->raw = $raw;
		$this->rawPercent = $rawPercent;
		$this->rawChange = $rawChange;
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
	 * Get the usage percent.
	 *
	 * @return float
	 */
	public function getUsagePercent() : float
	{
		return $this->usagePercent;
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
	 * Get the form icon.
	 *
	 * @return string
	 */
	public function getFormIcon() : string
	{
		return $this->formIcon;
	}

	/**
	 * Get the lead usage percent.
	 *
	 * @return float
	 */
	public function getLeadUsagePercent() : float
	{
		return $this->leadUsagePercent;
	}

	/**
	 * Get the lead usage change.
	 *
	 * @return float
	 */
	public function getLeadUsageChange() : float
	{
		return $this->leadUsageChange;
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
}
