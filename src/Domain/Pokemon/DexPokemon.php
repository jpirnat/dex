<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Pokemon;

use Jp\Dex\Domain\Abilities\DexPokemonAbility;
use Jp\Dex\Domain\Types\DexType;

final class DexPokemon
{
	private string $icon;
	private string $identifier;
	private string $name;

	/** @var DexType[] $types */
	private array $types;

	/** @var DexPokemonAbility[] $abilities */
	private array $abilities;

	/** @var int[] $baseStats */
	private array $baseStats;

	private int $bst;
	private int $sort;

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
	 * @param int $sort
	 */
	public function __construct(
		string $icon,
		string $identifier,
		string $name,
		array $types,
		array $abilities,
		array $baseStats,
		int $bst,
		int $sort
	) {
		$this->icon = $icon;
		$this->identifier = $identifier;
		$this->name = $name;
		$this->types = $types;
		$this->abilities = $abilities;
		$this->baseStats = $baseStats;
		$this->bst = $bst;
		$this->sort = $sort;
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

	/**
	 * Get the Pokémon's sort value.
	 *
	 * @return int
	 */
	public function getSort() : int
	{
		return $this->sort;
	}
}
