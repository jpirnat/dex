<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\DexMove;

use Jp\Dex\Domain\Abilities\DexPokemonAbility;
use Jp\Dex\Domain\Types\DexType;

final class DexMovePokemon
{
	private array $versionGroupData;
	private string $icon;
	private string $identifier;
	private string $name;

	/** @var DexType[] $types */
	private array $types;

	/** @var DexPokemonAbility[] $abilities */
	private array $abilities;

	/** @var int[] $baseStats */
	private array $baseStats;

	private int $baseStatTotal;
	private int $sort;


	/**
	 * Constructor.
	 *
	 * @param DexType[] $types
	 * @param DexPokemonAbility[] $abilities
	 * @param int[] $baseStats
	 */
	public function __construct(
		array $versionGroupData,
		string $icon,
		string $identifier,
		string $name,
		array $types,
		array $abilities,
		array $baseStats,
		int $baseStatTotal,
		int $sort
	) {
		$this->versionGroupData = $versionGroupData;
		$this->icon = $icon;
		$this->identifier = $identifier;
		$this->name = $name;
		$this->types = $types;
		$this->abilities = $abilities;
		$this->baseStats = $baseStats;
		$this->baseStatTotal = $baseStatTotal;
		$this->sort = $sort;
	}


	/**
	 * Get the version group data.
	 */
	public function getVersionGroupData() : array
	{
		return $this->versionGroupData;
	}

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
	 */
	public function getBaseStatTotal() : int
	{
		return $this->baseStatTotal;
	}

	/**
	 * Get the Pokémon's sort value.
	 */
	public function getSort() : int
	{
		return $this->sort;
	}
}
