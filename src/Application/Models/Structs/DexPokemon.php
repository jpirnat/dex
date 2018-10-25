<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\Structs;

class DexPokemon
{
	/** @var string $formIcon */
	private $formIcon;

	/** @var string $pokemonIdentifier */
	private $pokemonIdentifier;

	/** @var string $pokemonName */
	private $pokemonName;

	/** @var DexPokemonType[] $types */
	private $types;

	/** @var DexPokemonAbility[] $abilities */
	private $abilities;

	/** @var int $baseStats */
	private $baseStats;

	/**
	 * Constructor.
	 *
	 * @param string $formIcon
	 * @param string $pokemonIdentifier
	 * @param string $pokemonName
	 * @param DexPokemonType[] $types
	 * @param DexPokemonAbility[] $abilities
	 * @param int[] $baseStats
	 */
	public function __construct(
		string $formIcon,
		string $pokemonIdentifier,
		string $pokemonName,
		array $types,
		array $abilities,
		array $baseStats
	) {
		$this->formIcon = $formIcon;
		$this->pokemonIdentifier = $pokemonIdentifier;
		$this->pokemonName = $pokemonName;
		$this->types = $types;
		$this->abilities = $abilities;
		$this->baseStats = $baseStats;
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
	 * Get the Pokémon identifier.
	 *
	 * @return string
	 */
	public function getPokemonIdentifier() : string
	{
		return $this->pokemonIdentifier;
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
	 * Get the types.
	 *
	 * @return DexPokemonType[]
	 */
	public function getTypes() : array
	{
		return $this->types;
	}

	/**
	 * Get the abilities.
	 *
	 * @return DexPokemonAbility[]
	 */
	public function getAbilities() : array
	{
		return $this->abilities;
	}

	/**
	 * Get the base stats.
	 *
	 * @return int[]
	 */
	public function getBaseStats() : array
	{
		return $this->baseStats;
	}
}
