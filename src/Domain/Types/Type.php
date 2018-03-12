<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Types;

use Jp\Dex\Domain\Moves\CategoryId;

class Type
{
	/** @var TypeId $id */
	private $id;

	/** @var string $identifier */
	private $identifier;

	/** @var CategoryId|null $categoryId */
	private $categoryId;

	/** @var int|null $hiddenPowerIndex */
	private $hiddenPowerIndex;

	/** @var string $colorCode "#rrggbb" */
	private $colorCode;

	/**
	 * Constructor.
	 *
	 * @param TypeId $typeId
	 * @param string $identifier
	 * @param CategoryId|null $categoryId
	 * @param int|null $hiddenPowerIndex
	 * @param string $colorCode
	 */
	public function __construct(
		TypeId $typeId,
		string $identifier,
		?CategoryId $categoryId,
		?int $hiddenPowerIndex,
		string $colorCode
	) {
		$this->id = $typeId;
		$this->identifier = $identifier;
		$this->categoryId = $categoryId;
		$this->hiddenPowerIndex = $hiddenPowerIndex;
		$this->colorCode = $colorCode;
	}

	/**
	 * Get the type's id.
	 *
	 * @return TypeId
	 */
	public function getId() : TypeId
	{
		return $this->id;
	}

	/**
	 * Get the type's identifier.
	 *
	 * @return string
	 */
	public function getIdentifier() : string
	{
		return $this->identifier;
	}

	/**
	 * Get the type's category id.
	 *
	 * @return CategoryId|null
	 */
	public function getCategoryId() : ?CategoryId
	{
		return $this->categoryId;
	}

	/**
	 * Get the type's hidden power index.
	 *
	 * @return int|null
	 */
	public function getHiddenPowerIndex() : ?int
	{
		return $this->hiddenPowerIndex;
	}

	/**
	 * Get the type's color code.
	 *
	 * @return string
	 */
	public function getColorCode() : string
	{
		return $this->colorCode;
	}
}
