<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Versions;

final readonly class VersionGroup
{
	public function __construct(
		private(set) VersionGroupId $id,
		private(set) string $identifier,
		private(set) GenerationId $generationId,
		private(set) string $abbreviation,
		private(set) bool $hasBreeding,
		private(set) int $stepsPerEggCycle,
		private(set) string $statFormulaType,
		private(set) int $maxIv,
		private(set) bool $hasIvBasedHiddenPower,
		private(set) bool $hasEvBasedStats,
		private(set) bool $hasEvYields,
		private(set) int $maxEvsPerStat,
		private(set) bool $hasTransferMoves,
		private(set) bool $hasAbilities,
		private(set) bool $hasNatures,
		private(set) bool $hasCharacteristics,
		private(set) int $sort,
	) {}
}
