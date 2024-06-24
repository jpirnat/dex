<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Formats;

use Jp\Dex\Domain\Versions\GenerationId;
use Jp\Dex\Domain\Versions\VersionGroupId;

final readonly class Format
{
	public function __construct(
		private FormatId $id,
		private string $identifier,
		private string $name,
		private GenerationId $generationId,
		private VersionGroupId $versionGroupId,
		private int $level,
		private int $fieldSize,
		private string $smogonDexIdentifier,
	) {}

	/**
	 * Get the format's id.
	 */
	public function getId() : FormatId
	{
		return $this->id;
	}

	/**
	 * Get the format's identifier.
	 */
	public function getIdentifier() : string
	{
		return $this->identifier;
	}

	/**
	 * Get the format's name.
	 */
	public function getName() : string
	{
		return $this->name;
	}

	/**
	 * Get the format's generation id.
	 */
	public function getGenerationId() : GenerationId
	{
		return $this->generationId;
	}

	/**
	 * Get the format's version group id.
	 */
	public function getVersionGroupId() : VersionGroupId
	{
		return $this->versionGroupId;
	}

	/**
	 * Get the format's level.
	 */
	public function getLevel() : int
	{
		return $this->level;
	}

	/**
	 * Get the format's field size.
	 */
	public function getFieldSize() : int
	{
		return $this->fieldSize;
	}

	/**
	 * Get the format's Smogon dex identifier.
	 */
	public function getSmogonDexIdentifier() : string
	{
		return $this->smogonDexIdentifier;
	}
}
