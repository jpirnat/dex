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
		private bool $hasTypedHiddenPower,
		private bool $hasIvs,
		private bool $hasEvs,
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

	public function hasTypedHiddenPower() : bool
	{
		return $this->hasTypedHiddenPower;
	}

	public function hasIvs() : bool
	{
		return $this->hasIvs;
	}

	public function hasEvs() : bool
	{
		return $this->hasEvs;
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
