<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\MoveUsageMonth;

class MoveUsageData
{
	/** @var string $pokemonName */
	private $pokemonName;

	/** @var string $pokemonIdentifier */
	private $pokemonIdentifier;

	/** @var string $formIcon */
	private $formIcon;

	/** @var float $pokemonPercent */
	private $pokemonPercent;

	/** @var float $movePercent */
	private $movePercent;

	/** @var float $usagePercent */
	private $usagePercent;

	/**
	 * Constructor.
	 *
	 * @param string $pokemonName
	 * @param string $pokemonIdentifier
	 * @param string $formIcon
	 * @param float $pokemonPercent
	 * @param float $movePercent
	 * @param float $usagePercent
	 */
	public function __construct(
		string $pokemonName,
		string $pokemonIdentifier,
		string $formIcon,
		float $pokemonPercent,
		float $movePercent,
		float $usagePercent
	) {
		$this->pokemonName = $pokemonName;
		$this->pokemonIdentifier = $pokemonIdentifier;
		$this->formIcon = $formIcon;
		$this->pokemonPercent = $pokemonPercent;
		$this->movePercent = $movePercent;
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
	 * Get the move percent.
	 *
	 * @return float
	 */
	public function getMovePercent() : float
	{
		return $this->movePercent;
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
