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
		private string $statFormulaType,
		private int $maxIv,
		private bool $hasIvBasedHiddenPower,
		private bool $hasEvBasedStats,
		private bool $hasEvYields,
		private int $maxEvsPerStat,
		private bool $hasTransferMoves,
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

	public function getStatFormulaType() : string
	{
		return $this->statFormulaType;
	}

	public function getMaxIv() : int
	{
		return $this->maxIv;
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

	public function getMaxEvsPerStat() : int
	{
		return $this->maxEvsPerStat;
	}

	public function hasTransferMoves() : bool
	{
		return $this->hasTransferMoves;
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
