<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Versions;

final readonly class VersionGroup
{
	public function __construct(
		private VersionGroupId $id,
		private string $identifier,
		private GenerationId $generationId,
		private string $icon,
		private string $abbreviation,
		private int $sort,
	) {}

	/**
	 * Get the version group's id.
	 */
	public function getId() : VersionGroupId
	{
		return $this->id;
	}

	/**
	 * Get the version group's identifier.
	 */
	public function getIdentifier() : string
	{
		return $this->identifier;
	}

	/**
	 * Get the version group's generation id.
	 */
	public function getGenerationId() : GenerationId
	{
		return $this->generationId;
	}

	/**
	 * Get the version group's icon.
	 */
	public function getIcon() : string
	{
		return $this->icon;
	}

	/**
	 * Get the version group's abbreviation.
	 */
	public function getAbbreviation() : string
	{
		return $this->abbreviation;
	}

	/**
	 * Get the version group's sort value.
	 */
	public function getSort() : int
	{
		return $this->sort;
	}
}
