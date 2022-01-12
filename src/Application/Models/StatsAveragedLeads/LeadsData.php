<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\StatsAveragedLeads;

final class LeadsData
{
	public function __construct(
		private int $rank,
		private string $pokemonName,
		private int $months,
		private string $pokemonIdentifier,
		private string $formIcon,
		private float $leadUsagePercent,
		private int $raw,
		private float $rawPercent,
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
	 */
	public function getRank() : int
	{
		return $this->rank;
	}

	/**
	 * Get the Pokémon name.
	 */
	public function getPokemonName() : string
	{
		return $this->pokemonName;
	}

	/**
	 * Get the months.
	 */
	public function getMonths() : int
	{
		return $this->months;
	}

	/**
	 * Get the Pokémon identifier.
	 */
	public function getPokemonIdentifier() : string
	{
		return $this->pokemonIdentifier;
	}

	/**
	 * Get the form icon.
	 */
	public function getFormIcon() : string
	{
		return $this->formIcon;
	}

	/**
	 * Get the lead usage percent.
	 */
	public function getLeadUsagePercent() : float
	{
		return $this->leadUsagePercent;
	}

	/**
	 * Get the raw.
	 */
	public function getRaw() : int
	{
		return $this->raw;
	}

	/**
	 * Get the raw percent.
	 */
	public function getRawPercent() : float
	{
		return $this->rawPercent;
	}
}
