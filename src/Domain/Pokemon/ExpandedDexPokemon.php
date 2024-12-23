<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Pokemon;

use Jp\Dex\Domain\Abilities\ExpandedDexPokemonAbility;
use Jp\Dex\Domain\EggGroups\DexEggGroup;
use Jp\Dex\Domain\ExperienceGroups\DexExperienceGroup;
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
		private(set) string $identifier,
		private(set) string $name,
		private(set) string $sprite,
		/** @var DexType[] $types */
		private(set) array $types,
		/** @var ExpandedDexPokemonAbility[] $abilities */
		private(set) array $abilities,
		/** @var int[] $baseStats Indexed by stat identifier. */
		private(set) array $baseStats,
		private(set) int $bst,
		private(set) int $baseExperience,
		/** @var int[] $evYield Indexed by stat identifier. */
		private(set) array $evYield,
		private(set) int $evTotal,
		private(set) int $catchRate,
		private(set) int $baseFriendship,
		private(set) DexExperienceGroup $experienceGroup,
		/** @var DexEggGroup[] $eggGroups */
		private(set) array $eggGroups,
		private(set) GenderRatio $genderRatio,
		private(set) int $eggCycles,
		private(set) int $stepsToHatch,
	) {}
}
