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
		private(set) VersionGroupId $versionGroupId,
		private(set) PokemonId $pokemonId,
		private(set) TypeId $type1Id,
		private(set) ?TypeId $type2Id,
		private(set) ?AbilityId $ability1Id,
		private(set) ?AbilityId $ability2Id,
		private(set) ?AbilityId $ability3Id,
		private(set) int $baseHp,
		private(set) int $baseAtk,
		private(set) int $baseDef,
		private(set) int $baseSpa,
		private(set) int $baseSpd,
		private(set) int $baseSpe,
		private(set) int $baseSpc,
		private(set) ?EggGroupId $eggGroup1Id,
		private(set) ?EggGroupId $eggGroup2Id,
		private(set) int $baseExperience,
		private(set) int $evHp,
		private(set) int $evAtk,
		private(set) int $evDef,
		private(set) int $evSpa,
		private(set) int $evSpd,
		private(set) int $evSpe,
	) {}

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
}
