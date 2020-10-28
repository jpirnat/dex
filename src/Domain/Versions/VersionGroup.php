<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Versions;

final class VersionGroup
{
	public function __construct(
		private VersionGroupId $id,
		private string $identifier,
		private GenerationId $generationId,
		private string $icon,
		private int $sort,
	) {}

	/**
	 * Get the version group's id.
	 *
	 * @return VersionGroupId
	 */
	public function getId() : VersionGroupId
	{
		return $this->id;
	}

	/**
	 * Get the version group's identifier.
	 *
	 * @return string
	 */
	public function getIdentifier() : string
	{
		return $this->identifier;
	}

	/**
	 * Get the version group's generation id.
	 *
	 * @return GenerationId
	 */
	public function getGenerationId() : GenerationId
	{
		return $this->generationId;
	}

	/**
	 * Get the version group's icon.
	 *
	 * @return string
	 */
	public function getIcon() : string
	{
		return $this->icon;
	}

	/**
	 * Get the version group's sort value.
	 *
	 * @return int
	 */
	public function getSort() : int
	{
		return $this->sort;
	}
}
