<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\StatsAveragedLeads;

final class LeadsData
{
	/** @var int $rank */
	private $rank;

	/** @var string $pokemonName */
	private $pokemonName;

	/** @var int $months */
	private $months;

	/** @var string $pokemonIdentifier */
	private $pokemonIdentifier;

	/** @var string $formIcon */
	private $formIcon;

	/** @var float $leadUsagePercent */
	private $leadUsagePercent;

	/** @var int $raw */
	private $raw;

	/** @var float $rawPercent */
	private $rawPercent;

	/**
	 * Constructor.
	 *
	 * @param int $rank
	 * @param string $pokemonName
	 * @param int $months
	 * @param string $pokemonIdentifier
	 * @param string $formIcon
	 * @param float $leadUsagePercent
	 * @param int $raw
	 * @param float $rawPercent
	 */
	public function __construct(
		int $rank,
		string $pokemonName,
		int $months,
		string $pokemonIdentifier,
		string $formIcon,
		float $leadUsagePercent,
		int $raw,
		float $rawPercent
	) {
		$this->rank = $rank;
		$this->pokemonName = $pokemonName;
		$this->months = $months;
		$this->pokemonIdentifier = $pokemonIdentifier;
		$this->formIcon = $formIcon;
		$this->leadUsagePercent = $leadUsagePercent;
		$this->raw = $raw;
		$this->rawPercent = $rawPercent;
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
	 * Get the months.
	 *
	 * @return int
	 */
	public function getMonths() : int
	{
		return $this->months;
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
}
