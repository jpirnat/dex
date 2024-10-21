<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Pokemon;

use Jp\Dex\Domain\Abilities\ExpandedDexPokemonAbility;
use Jp\Dex\Domain\EggGroups\DexEggGroup;
use Jp\Dex\Domain\Types\DexType;

final readonly class ExpandedDexPokemon
{
	/**
	 * @param DexType[] $types
	 * @param ExpandedDexPokemonAbility[] $abilities
	 * @param int[] $baseStats
	 * @param DexEggGroup[] $eggGroups
	 * @param int[] $evYield
	 */
	public function __construct(
		private string $identifier,
		private string $name,
		private string $sprite,
		/** @var DexType[] $types */ private array $types,
		/** @var ExpandedDexPokemonAbility[] $abilities */ private array $abilities,
		/** @var int[] $baseStats */ private array $baseStats,
		private int $bst,
		/** @var DexEggGroup[] $eggGroups */ private array $eggGroups,
		private GenderRatio $genderRatio,
		private int $eggCycles,
		private int $stepsToHatch,
		private int $baseExperience,
		/** @var int[] $evYield */ private array $evYield,
		private int $evTotal,
	) {}

	public function getIdentifier() : string
	{
		return $this->identifier;
	}

	public function getName() : string
	{
		return $this->name;
	}

	public function getSprite() : string
	{
		return $this->sprite;
	}

	/**
	 * @return DexType[]
	 */
	public function getTypes() : array
	{
		return $this->types;
	}

	/**
	 * @return ExpandedDexPokemonAbility[]
	 */
	public function getAbilities() : array
	{
		return $this->abilities;
	}

	/**
	 * @return int[] Indexed by stat identifier.
	 */
	public function getBaseStats() : array
	{
		return $this->baseStats;
	}

	public function getBst() : int
	{
		return $this->bst;
	}

	/**
	 * @return DexEggGroup[]
	 */
	public function getEggGroups() : array
	{
		return $this->eggGroups;
	}

	public function getGenderRatio() : GenderRatio
	{
		return $this->genderRatio;
	}

	public function getEggCycles() : int
	{
		return $this->eggCycles;
	}

	public function getStepsToHatch() : int
	{
		return $this->stepsToHatch;
	}

	public function getBaseExperience() : int
	{
		return $this->baseExperience;
	}

	/**
	 * @return int[] Indexed by stat identifier.
	 */
	public function getEvYield() : array
	{
		return $this->evYield;
	}

	public function getEvTotal() : int
	{
		return $this->evTotal;
	}
}
