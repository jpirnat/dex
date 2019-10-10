<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Usage;

final class StatsMovePokemon
{
	/** @var string $icon */
	private $icon;

	/** @var string $identifier */
	private $identifier;

	/** @var string $name */
	private $name;

	/** @var float $pokemonPercent */
	private $pokemonPercent;

	/** @var float $movePercent */
	private $movePercent;

	/** @var float $usagePercent */
	private $usagePercent;

	/** @var float $usageChange */
	private $usageChange;

	/**
	 * Constructor.
	 *
	 * @param string $icon
	 * @param string $identifier
	 * @param string $name
	 * @param float $pokemonPercent
	 * @param float $movePercent
	 * @param float $usagePercent
	 * @param float $usageChange
	 */
	public function __construct(
		string $icon,
		string $identifier,
		string $name,
		float $pokemonPercent,
		float $movePercent,
		float $usagePercent,
		float $usageChange
	) {
		$this->icon = $icon;
		$this->identifier = $identifier;
		$this->name = $name;
		$this->pokemonPercent = $pokemonPercent;
		$this->movePercent = $movePercent;
		$this->usagePercent = $usagePercent;
		$this->usageChange = $usageChange;
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
	 * Get the Pokémon's Pokémon percent.
	 *
	 * @return float
	 */
	public function getPokemonPercent() : float
	{
		return $this->pokemonPercent;
	}

	/**
	 * Get the Pokémon's move percent.
	 *
	 * @return float
	 */
	public function getMovePercent() : float
	{
		return $this->movePercent;
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
}
