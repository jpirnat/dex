<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\StatsAbility;

class AbilityUsageData
{
	/** @var string $pokemonName */
	private $pokemonName;

	/** @var string $pokemonIdentifier */
	private $pokemonIdentifier;

	/** @var string $formIcon */
	private $formIcon;

	/** @var float $pokemonPercent */
	private $pokemonPercent;

	/** @var float $abilityPercent */
	private $abilityPercent;

	/** @var float $usagePercent */
	private $usagePercent;

	/** @var float $change */
	private $change;

	/**
	 * Constructor.
	 *
	 * @param string $pokemonName
	 * @param string $pokemonIdentifier
	 * @param string $formIcon
	 * @param float $pokemonPercent
	 * @param float $abilityPercent
	 * @param float $usagePercent
	 * @param float $change
	 */
	public function __construct(
		string $pokemonName,
		string $pokemonIdentifier,
		string $formIcon,
		float $pokemonPercent,
		float $abilityPercent,
		float $usagePercent,
		float $change
	) {
		$this->pokemonName = $pokemonName;
		$this->pokemonIdentifier = $pokemonIdentifier;
		$this->formIcon = $formIcon;
		$this->pokemonPercent = $pokemonPercent;
		$this->abilityPercent = $abilityPercent;
		$this->usagePercent = $usagePercent;
		$this->change = $change;
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
	 * Get the form icon.
	 *
	 * @return string
	 */
	public function getFormIcon() : string
	{
		return $this->formIcon;
	}

	/**
	 * Get the Pokémon percent.
	 *
	 * @return float
	 */
	public function getPokemonPercent() : float
	{
		return $this->pokemonPercent;
	}

	/**
	 * Get the ability percent.
	 *
	 * @return float
	 */
	public function getAbilityPercent() : float
	{
		return $this->abilityPercent;
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
	 * Get the change.
	 *
	 * @return float
	 */
	public function getChange() : float
	{
		return $this->change;
	}
}
