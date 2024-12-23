<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Moves;

use Jp\Dex\Domain\Categories\CategoryId;
use Jp\Dex\Domain\Moves\Inflictions\InflictionId;
use Jp\Dex\Domain\Moves\Qualities\QualityId;
use Jp\Dex\Domain\Moves\Targets\TargetId;
use Jp\Dex\Domain\Moves\ZPowerEffects\ZPowerEffectId;
use Jp\Dex\Domain\Types\TypeId;
use Jp\Dex\Domain\Versions\VersionGroupId;

final readonly class VgMove
{
	public function __construct(
		private(set) VersionGroupId $versionGroupId,
		private(set) MoveId $moveId,
		private(set) TypeId $typeId,
		private(set) ?QualityId $qualityId,
		private(set) CategoryId $categoryId,
		private(set) int $power,
		private(set) int $accuracy,
		private(set) int $pp,
		private(set) int $priority,
		private(set) int $minHits,
		private(set) int $maxHits,
		private(set) InflictionId $inflictionId,
		private(set) int $inflictionPercent,
		private(set) int $minTurns,
		private(set) int $maxTurns,
		private(set) int $critStage,
		private(set) int $flinchPercent,
		private(set) int $effect,
		private(set) ?int $effectPercent,
		private(set) int $recoilPercent,
		private(set) int $healPercent,
		private(set) TargetId $targetId,
		private(set) ?MoveId $zMoveId,
		private(set) ?int $zBasePower,
		private(set) ?ZPowerEffectId $zPowerEffectId,
		private(set) ?MoveId $maxMoveId,
		private(set) ?int $maxPower,
	) {}
}
