<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Pokemon;

use Jp\Dex\Domain\Abilities\DexPokemonAbility;
use Jp\Dex\Domain\Types\DexType;

class DexPokemon
{
	/** @var string $icon */
	private $icon;

	/** @var string $identifier */
	private $identifier;

	/** @var string $name */
	private $name;

	/** @var DexType[] $types */
	private $types;

	/** @var DexPokemonAbility[] $abilities */
	private $abilities;

	/** @var int $baseStats */
	private $baseStats;

	/** @var int $bst */
	private $bst;

	/**
	 * Constructor.
	 *
	 * @param string $icon
	 * @param string $identifier
	 * @param string $name
	 * @param DexType[] $types
	 * @param DexPokemonAbility[] $abilities
	 * @param int[] $baseStats
	 * @param int $bst
	 */
	public function __construct(
		string $icon,
		string $identifier,
		string $name,
		array $types,
		array $abilities,
		array $baseStats,
		int $bst
	) {
		$this->icon = $icon;
		$this->identifier = $identifier;
		$this->name = $name;
		$this->types = $types;
		$this->abilities = $abilities;
		$this->baseStats = $baseStats;
		$this->bst = $bst;
	}

	/**
	 * Get the Pokémon's icon.
	 *
	 * @return string
	 */
	public function getFormIcon() : string
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
	 * Get the Pokémon's types.
	 *
	 * @return DexType[]
	 */
	public function getTypes() : array
	{
		return $this->types;
	}

	/**
	 * Get the Pokémon's abilities.
	 *
	 * @return DexPokemonAbility[]
	 */
	public function getAbilities() : array
	{
		return $this->abilities;
	}

	/**
	 * Get the Pokémon's base stats.
	 *
	 * @return int[]
	 */
	public function getBaseStats() : array
	{
		return $this->baseStats;
	}

	/**
	 * Get the Pokémon's base stat total.
	 *
	 * @return int
	 */
	public function getBst() : int
	{
		return $this->bst;
	}
}
