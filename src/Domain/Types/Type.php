<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Types;

use Jp\Dex\Domain\Categories\CategoryId;

final readonly class Type
{
	public function __construct(
		private TypeId $id,
		private string $identifier,
		private ?CategoryId $categoryId,
		private string $symbolIcon,
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
	 * Get the type's category id.
	 */
	public function getCategoryId() : ?CategoryId
	{
		return $this->categoryId;
	}

	/**
	 * Get the type's symbol icon.
	 */
	public function getSymbolIcon() : string
	{
		return $this->symbolIcon;
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
