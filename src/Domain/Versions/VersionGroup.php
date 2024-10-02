<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Versions;

final readonly class VersionGroup
{
	public function __construct(
		private VersionGroupId $id,
		private string $identifier,
		private GenerationId $generationId,
		private string $abbreviation,
		private bool $hasBreeding,
		private int $stepsPerEggCycle,
		private bool $hasIvBasedStats,
		private bool $hasIvBasedHiddenPower,
		private bool $hasEvBasedStats,
		private bool $hasEvYields,
		private bool $hasAbilities,
		private bool $hasNatures,
		private bool $hasCharacteristics,
		private int $sort,
	) {}

	public function getId() : VersionGroupId
	{
		return $this->id;
	}

	public function getIdentifier() : string
	{
		return $this->identifier;
	}

	public function getGenerationId() : GenerationId
	{
		return $this->generationId;
	}

	public function getAbbreviation() : string
	{
		return $this->abbreviation;
	}

	public function hasBreeding() : bool
	{
		return $this->hasBreeding;
	}

	public function getStepsPerEggCycle() : int
	{
		return $this->stepsPerEggCycle;
	}

	public function hasIvBasedStats() : bool
	{
		return $this->hasIvBasedStats;
	}

	public function hasIvBasedHiddenPower() : bool
	{
		return $this->hasIvBasedHiddenPower;
	}

	public function hasEvBasedStats() : bool
	{
		return $this->hasEvBasedStats;
	}

	public function hasEvYields() : bool
	{
		return $this->hasEvYields;
	}

	public function hasAbilities() : bool
	{
		return $this->hasAbilities;
	}

	public function hasNatures() : bool
	{
		return $this->hasNatures;
	}

	public function hasCharacteristics() : bool
	{
		return $this->hasCharacteristics;
	}

	public function getSort() : int
	{
		return $this->sort;
	}
}
