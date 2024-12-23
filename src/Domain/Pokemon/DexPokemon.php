<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Pokemon;

use Jp\Dex\Domain\Abilities\DexPokemonAbility;
use Jp\Dex\Domain\EggGroups\DexEggGroup;
use Jp\Dex\Domain\Types\DexType;

final readonly class DexPokemon
{
	public function __construct(
		private(set) string $icon,
		private(set) string $identifier,
		private(set) string $name,
		/** @var DexType[] $types */
		private(set) array $types,
		/** @var DexPokemonAbility[] $abilities */
		private(set) array $abilities,
		/** @var int[] $baseStats Indexed by stat identifier. */
		private(set) array $baseStats,
		private(set) int $bst,
		/** @var DexEggGroup[] $eggGroups */
		private(set) array $eggGroups,
		private(set) GenderRatio $genderRatio,
		private(set) int $eggCycles,
		private(set) int $stepsToHatch,
		/** @var int[] $evYieldIndexed by stat identifier. */
		private(set) array $evYield,
		private(set) int $sort,
	) {}
}
