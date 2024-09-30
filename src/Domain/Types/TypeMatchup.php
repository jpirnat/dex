<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Types;

use Jp\Dex\Domain\Versions\GenerationId;

final readonly class TypeMatchup
{
	public function __construct(
		private GenerationId $generationId,
		private string $attackingTypeIdentifier,
		private string $defendingTypeIdentifier,
		private float $multiplier,
	) {}

	public function getGenerationId() : GenerationId
	{
		return $this->generationId;
	}

	public function getAttackingTypeIdentifier() : string
	{
		return $this->attackingTypeIdentifier;
	}

	public function getDefendingTypeIdentifier() : string
	{
		return $this->defendingTypeIdentifier;
	}

	public function getMultiplier() : float
	{
		return $this->multiplier;
	}
}
