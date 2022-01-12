<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Types;

use Jp\Dex\Domain\Versions\GenerationId;

final class TypeMatchup
{
	public function __construct(
		private GenerationId $generationId,
		private TypeId $attackingTypeId,
		private TypeId $defendingTypeId,
		private float $multiplier,
	) {}

	/**
	 * Get the type matchup's generation id.
	 */
	public function getGenerationId() : GenerationId
	{
		return $this->generationId;
	}

	/**
	 * Get the type matchup's attacking type id.
	 */
	public function getAttackingTypeId() : TypeId
	{
		return $this->attackingTypeId;
	}

	/**
	 * Get the type matchup's defending type id.
	 */
	public function getDefendingTypeId() : TypeId
	{
		return $this->defendingTypeId;
	}

	/**
	 * Get the type matchup's multiplier.
	 */
	public function getMultiplier() : float
	{
		return $this->multiplier;
	}
}
