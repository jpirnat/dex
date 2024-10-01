<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Pokemon;

use Jp\Dex\Domain\Abilities\AbilityId;
use Jp\Dex\Domain\EggGroups\EggGroupId;
use Jp\Dex\Domain\Types\TypeId;
use Jp\Dex\Domain\Versions\VersionGroupId;

final readonly class VgPokemon
{
	public function __construct(
		private VersionGroupId $versionGroupId,
		private PokemonId $pokemonId,
		private TypeId $type1Id,
		private ?TypeId $type2Id,
		private ?AbilityId $ability1Id,
		private ?AbilityId $ability2Id,
		private ?AbilityId $ability3Id,
		private int $baseHp,
		private int $baseAtk,
		private int $baseDef,
		private int $baseSpa,
		private int $baseSpd,
		private int $baseSpe,
		private int $baseSpc,
		private ?EggGroupId $eggGroup1Id,
		private ?EggGroupId $eggGroup2Id,
		private int $baseExperience,
		private int $evHp,
		private int $evAtk,
		private int $evDef,
		private int $evSpa,
		private int $evSpd,
		private int $evSpe,
	) {}

	public function getVersionGroupId(): VersionGroupId
	{
		return $this->versionGroupId;
	}

	public function getPokemonId(): PokemonId
	{
		return $this->pokemonId;
	}

	public function getType1Id(): TypeId
	{
		return $this->type1Id;
	}

	public function getType2Id(): ?TypeId
	{
		return $this->type2Id;
	}

	/**
	 * @return TypeId[]
	 */
	public function getTypeIds(): array
	{
		$typeIds = [$this->type1Id];

		if ($this->type2Id) {
			$typeIds[] = $this->type2Id;
		}

		return $typeIds;
	}

	public function getAbility1Id(): ?AbilityId
	{
		return $this->ability1Id;
	}

	public function getAbility2Id(): ?AbilityId
	{
		return $this->ability2Id;
	}

	public function getAbility3Id(): ?AbilityId
	{
		return $this->ability3Id;
	}

	public function getBaseHp(): int
	{
		return $this->baseHp;
	}

	public function getBaseAtk(): int
	{
		return $this->baseAtk;
	}

	public function getBaseDef(): int
	{
		return $this->baseDef;
	}

	public function getBaseSpa(): int
	{
		return $this->baseSpa;
	}

	public function getBaseSpd(): int
	{
		return $this->baseSpd;
	}

	public function getBaseSpe(): int
	{
		return $this->baseSpe;
	}

	public function getBaseSpc(): int
	{
		return $this->baseSpc;
	}

	public function getEggGroup1Id(): ?EggGroupId
	{
		return $this->eggGroup1Id;
	}

	public function getEggGroup2Id(): ?EggGroupId
	{
		return $this->eggGroup2Id;
	}

	public function getBaseExperience(): int
	{
		return $this->baseExperience;
	}

	public function getEvHp(): int
	{
		return $this->evHp;
	}

	public function getEvAtk(): int
	{
		return $this->evAtk;
	}

	public function getEvDef(): int
	{
		return $this->evDef;
	}

	public function getEvSpa(): int
	{
		return $this->evSpa;
	}

	public function getEvSpd(): int
	{
		return $this->evSpd;
	}

	public function getEvSpe(): int
	{
		return $this->evSpe;
	}
}
