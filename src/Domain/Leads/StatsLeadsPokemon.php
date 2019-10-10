<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Leads;

final class StatsLeadsPokemon
{
	/** @var int $rank */
	private $rank;

	/** @var string $icon */
	private $icon;

	/** @var string $identifier */
	private $identifier;

	/** @var string $name */
	private $name;

	/** @var float $usagePercent */
	private $usagePercent;

	/** @var float $usageChange */
	private $usageChange;

	/** @var int $raw */
	private $raw;

	/** @var float $rawPercent */
	private $rawPercent;

	/**
	 * Constructor.
	 *
	 * @param int $rank
	 * @param string $icon
	 * @param string $identifier
	 * @param string $name
	 * @param float $usagePercent
	 * @param float $usageChange
	 * @param int $raw
	 * @param float $rawPercent
	 */
	public function __construct(
		int $rank,
		string $icon,
		string $identifier,
		string $name,
		float $usagePercent,
		float $usageChange,
		int $raw,
		float $rawPercent
	) {
		$this->rank = $rank;
		$this->icon = $icon;
		$this->identifier = $identifier;
		$this->name = $name;
		$this->usagePercent = $usagePercent;
		$this->usageChange = $usageChange;
		$this->raw = $raw;
		$this->rawPercent = $rawPercent;
	}

	/**
	 * Get the Pokémon's rank.
	 *
	 * @return int
	 */
	public function getRank() : int
	{
		return $this->rank;
	}

	/**
	 * Get the Pokémon's icon.
	 *
	 * @return string
	 */
	public function getIcon() : string
	{
		return $this->icon;
	}

	/**
	 * Get the Pokémon's identifier.
	 *
	 * @return string
	 */
	public function getIdentifier() : string
	{
		return $this->identifier;
	}

	/**
	 * Get the Pokémon's name.
	 *
	 * @return string
	 */
	public function getName() : string
	{
		return $this->name;
	}

	/**
	 * Get the Pokémon's usage percent.
	 *
	 * @return float
	 */
	public function getUsagePercent() : float
	{
		return $this->usagePercent;
	}

	/**
	 * Get the Pokémon's usage change.
	 *
	 * @return float
	 */
	public function getUsageChange() : float
	{
		return $this->usageChange;
	}

	/**
	 * Get the Pokémon's raw count.
	 *
	 * @return int
	 */
	public function getRaw() : int
	{
		return $this->raw;
	}

	/**
	 * Get the Pokémon's raw percent.
	 *
	 * @return float
	 */
	public function getRawPercent() : float
	{
		return $this->rawPercent;
	}
}
