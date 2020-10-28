<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Versions;

final class DexVersionGroup
{
	public function __construct(
		private VersionGroupId $id,
		private string $identifier,
		private GenerationId $generationId,
		private string $icon,
		private string $name,
	) {}

	/**
	 * Get the dex version group's id.
	 *
	 * @return VersionGroupId
	 */
	public function getId() : VersionGroupId
	{
		return $this->id;
	}

	/**
	 * Get the dex version group's identifier.
	 *
	 * @return string
	 */
	public function getIdentifier() : string
	{
		return $this->identifier;
	}

	/**
	 * Get the dex version group's generation id.
	 *
	 * @return GenerationId
	 */
	public function getGenerationId() : GenerationId
	{
		return $this->generationId;
	}

	/**
	 * Get the dex version group's icon.
	 *
	 * @return string
	 */
	public function getIcon() : string
	{
		return $this->icon;
	}

	/**
	 * Get the dex version group's name.
	 *
	 * @return string
	 */
	public function getName() : string
	{
		return $this->name;
	}
}
