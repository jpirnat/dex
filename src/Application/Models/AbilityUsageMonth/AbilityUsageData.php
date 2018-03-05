<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\AbilityUsageMonth;

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

	/**
	 * Constructor.
	 *
	 * @param string $pokemonName
	 * @param string $pokemonIdentifier
	 * @param string $formIcon
	 * @param float $pokemonPercent
	 * @param float $abilityPercent
	 * @param float $usagePercent
	 */
	public function __construct(
		string $pokemonName,
		string $pokemonIdentifier,
		string $formIcon,
		float $pokemonPercent,
		float $abilityPercent,
		float $usagePercent
	) {
		$this->pokemonName = $pokemonName;
		$this->pokemonIdentifier = $pokemonIdentifier;
		$this->formIcon = $formIcon;
		$this->pokemonPercent = $pokemonPercent;
		$this->abilityPercent = $abilityPercent;
		$this->usagePercent = $usagePercent;
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
}
