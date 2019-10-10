<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Usage;

final class StatsAbilityPokemon
{
	/** @var string $icon */
	private $icon;

	/** @var string $identifier */
	private $identifier;

	/** @var string $name */
	private $name;

	/** @var float $pokemonPercent */
	private $pokemonPercent;

	/** @var float $abilityPercent */
	private $abilityPercent;

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
	 * @param float $abilityPercent
	 * @param float $usagePercent
	 * @param float $usageChange
	 */
	public function __construct(
		string $icon,
		string $identifier,
		string $name,
		float $pokemonPercent,
		float $abilityPercent,
		float $usagePercent,
		float $usageChange
	) {
		$this->icon = $icon;
		$this->identifier = $identifier;
		$this->name = $name;
		$this->pokemonPercent = $pokemonPercent;
		$this->abilityPercent = $abilityPercent;
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
	 * Get the Pokémon's ability percent.
	 *
	 * @return float
	 */
	public function getAbilityPercent() : float
	{
		return $this->abilityPercent;
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
