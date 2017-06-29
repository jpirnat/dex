<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\MovesetPokemonMonth;

class TeammateData
{
	/** @var string $pokemonName */
	private $pokemonName;

	/** @var string $pokemonIdentifier */
	private $pokemonIdentifier;

	/** @var float $percent */
	private $percent;

	/**
	 * Constructor.
	 *
	 * @param string $pokemonName
	 * @param string $pokemonIdentifier
	 * @param float $percent
	 */
	public function __construct(
		string $pokemonName,
		string $pokemonIdentifier,
		float $percent
	) {
		$this->pokemonName = $pokemonName;
		$this->pokemonIdentifier = $pokemonIdentifier;
		$this->percent = $percent;
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
	 * Get the percent.
	 *
	 * @return float
	 */
	public function getPercent() : float
	{
		return $this->percent;
	}
}
