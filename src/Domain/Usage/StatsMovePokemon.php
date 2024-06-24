<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Usage;

final readonly class StatsMovePokemon
{
	public function __construct(
		private string $icon,
		private string $identifier,
		private string $name,
		private float $pokemonPercent,
		private float $movePercent,
		private float $usagePercent,
		private float $usageChange,
	) {}

	/**
	 * Get the Pokémon's icon.
	 */
	public function getIcon() : string
	{
		return $this->icon;
	}

	/**
	 * Get the Pokémon's identifier.
	 */
	public function getIdentifier() : string
	{
		return $this->identifier;
	}

	/**
	 * Get the Pokémon's name.
	 */
	public function getName() : string
	{
		return $this->name;
	}

	/**
	 * Get the Pokémon's Pokémon percent.
	 */
	public function getPokemonPercent() : float
	{
		return $this->pokemonPercent;
	}

	/**
	 * Get the Pokémon's move percent.
	 */
	public function getMovePercent() : float
	{
		return $this->movePercent;
	}

	/**
	 * Get the Pokémon's usage percent.
	 */
	public function getUsagePercent() : float
	{
		return $this->usagePercent;
	}

	/**
	 * Get the Pokémon's usage change.
	 */
	public function getUsageChange() : float
	{
		return $this->usageChange;
	}
}
