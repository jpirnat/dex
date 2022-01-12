<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Types;

use Jp\Dex\Domain\Categories\CategoryId;
use Jp\Dex\Domain\Versions\GenerationId;

final class Type
{
	public function __construct(
		private TypeId $id,
		private string $identifier,
		private GenerationId $introducedInGenerationId,
		private ?CategoryId $categoryId,
		private ?int $hiddenPowerIndex,
		private string $colorCode,
	) {}

	/**
	 * Get the type's id.
	 */
	public function getId() : TypeId
	{
		return $this->id;
	}

	/**
	 * Get the type's identifier.
	 */
	public function getIdentifier() : string
	{
		return $this->identifier;
	}

	/**
	 * Get the generation id this type was introduced in.
	 */
	public function getIntroducedInGenerationId() : GenerationId
	{
		return $this->introducedInGenerationId;
	}

	/**
	 * Get the type's category id.
	 */
	public function getCategoryId() : ?CategoryId
	{
		return $this->categoryId;
	}

	/**
	 * Get the type's hidden power index.
	 */
	public function getHiddenPowerIndex() : ?int
	{
		return $this->hiddenPowerIndex;
	}

	/**
	 * Get the type's color code ("#rrggbb").
	 */
	public function getColorCode() : string
	{
		return $this->colorCode;
	}
}
