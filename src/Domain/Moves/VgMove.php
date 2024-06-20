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

final class VgMove
{
	public function __construct(
		private VersionGroupId $versionGroupId,
		private MoveId $moveId,
		private TypeId $typeId,
		private ?QualityId $qualityId,
		private CategoryId $categoryId,
		private int $power,
		private int $accuracy,
		private int $pp,
		private int $priority,
		private int $minHits,
		private int $maxHits,
		private ?InflictionId $inflictionId,
		private int $inflictionPercent,
		private int $minTurns,
		private int $maxTurns,
		private int $critStage,
		private int $flinchPercent,
		private int $effect,
		private ?int $effectPercent,
		private int $recoilPercent,
		private int $healPercent,
		private TargetId $targetId,
		private ?MoveId $zMoveId,
		private ?int $zBasePower,
		private ?ZPowerEffectId $zPowerEffectId,
		private ?MoveId $maxMoveId,
		private ?int $maxPower,
	) {}

	/**
	 * Get the version group move's version group id.
	 */
	public function getVersionGroupId() : VersionGroupId
	{
		return $this->versionGroupId;
	}

	/**
	 * Get the version group move's move id.
	 */
	public function getMoveId() : MoveId
	{
		return $this->moveId;
	}

	/**
	 * Get the version group move's type id.
	 */
	public function getTypeId() : TypeId
	{
		return $this->typeId;
	}

	/**
	 * Get the version group move's quality id. This value only exists for
	 * generations 5 and above.
	 */
	public function getQualityId() : ?QualityId
	{
		return $this->qualityId;
	}

	/**
	 * Get the version group move's category id.
	 */
	public function getCategoryId() : CategoryId
	{
		return $this->categoryId;
	}

	/**
	 * Get the version group move's power.
	 */
	public function getPower() : int
	{
		return $this->power;
	}

	/**
	 * Get the version group move's accuracy.
	 */
	public function getAccuracy() : int
	{
		return $this->accuracy;
	}

	/**
	 * Get the version group move's PP.
	 */
	public function getPP() : int
	{
		return $this->pp;
	}

	/**
	 * Get the version group move's priority.
	 */
	public function getPriority() : int
	{
		return $this->priority;
	}

	/**
	 * Get the version group move's minimum number of hits (for moves like
	 * Double Slap and Water Shuriken).
	 */
	public function getMinHits() : int
	{
		return $this->minHits;
	}

	/**
	 * Get the version group move's maximum number of hits (for moves like
	 * Double Slap and Water Shuriken).
	 */
	public function getMaxHits() : int
	{
		return $this->maxHits;
	}

	/**
	 * Get the version group move's infliction id.
	 */
	public function getInflictionId() : ?InflictionId
	{
		return $this->inflictionId;
	}

	/**
	 * Get the version group move's infliction percent, the percent chance that
	 * the move will cause its infliction on the target.
	 */
	public function getInflictionPercent() : int
	{
		return $this->inflictionPercent;
	}

	/**
	 * Get the version group move's minimum number of turns for which the effect
	 * might last (for moves like Fire Spin and Spore).
	 */
	public function getMinTurns() : int
	{
		return $this->minTurns;
	}

	/**
	 * Get the version group move's maximum number of turns for which the effect
	 * might last (for moves like Fire Spin and Spore).
	 */
	public function getMaxTurns() : int
	{
		return $this->maxTurns;
	}

	/**
	 * Get the version group move's critical hit stage.
	 */
	public function getCritStage() : int
	{
		return $this->critStage;
	}

	/**
	 * Get the version group move's flinch percent, the percent chance that the
	 * move has of causing the target to flinch.
	 */
	public function getFlinchPercent() : int
	{
		return $this->flinchPercent;
	}

	/**
	 * Get the version group move's effect id.
	 */
	public function getEffect() : int
	{
		return $this->effect;
	}

	/**
	 * Get the version group move's effect percent. This value only exists for
	 * generations 2 through 4.
	 */
	public function getEffectPercent() : ?int
	{
		return $this->effectPercent;
	}

	/**
	 * Get the version group move's recoil percent, the percent of inflicted
	 * damage that the user takes back as recoil. Negative for actual recoil
	 * moves like Double Edge. Positive for moves like Giga Drain, for which the
	 * user recovers a percent of the inflicted damage.
	 */
	public function getRecoilPercent() : int
	{
		return $this->recoilPercent;
	}

	/**
	 * Get the version group move's heal percent, the percent of HP which the
	 * user heals upon using a move like Recover. Negative for Struggle, which
	 * does a fixed percent recoil to the user since generation 4.
	 */
	public function getHealPercent() : int
	{
		return $this->healPercent;
	}

	/**
	 * Get the version group move's target id.
	 */
	public function getTargetId() : TargetId
	{
		return $this->targetId;
	}

	/**
	 * Get the version group move's Z-Move id.
	 */
	public function getZMoveId() : ?MoveId
	{
		return $this->zMoveId;
	}

	/**
	 * Get the version group move's Z-Move's base power.
	 */
	public function getZBasePower() : ?int
	{
		return $this->zBasePower;
	}

	/**
	 * Get the version group move's Z-Move's Z-Power Effect id.
	 */
	public function getZPowerEffectId() : ?ZPowerEffectId
	{
		return $this->zPowerEffectId;
	}

	/**
	 * Get the version group move's Max Move id.
	 */
	public function getMaxMoveId() : ?MoveId
	{
		return $this->maxMoveId;
	}

	/**
	 * Get the version group move's Max Move's base power.
	 */
	public function getMaxPower() : ?int
	{
		return $this->maxPower;
	}
}
