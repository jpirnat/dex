<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Types;

use Jp\Dex\Domain\Moves\CategoryId;

class Type
{
	/** @var TypeId $id */
	protected $id;

	/** @var string $identifier */
	protected $identifier;

	/** @var CategoryId|null $categoryId */
	protected $categoryId;

	/** @var int|null $hiddenPowerIndex */
	protected $hiddenPowerIndex;

	/**
	 * Constructor.
	 *
	 * @param TypeId $typeId
	 * @param string $identifier
	 * @param CategoryId|null $categoryId
	 * @param int|null $hiddenPowerIndex
	 */
	public function __construct(
		TypeId $typeId,
		string $identifier,
		?CategoryId $categoryId,
		?int $hiddenPowerIndex
	) {
		$this->id = $typeId;
		$this->identifier = $identifier;
		$this->categoryId = $categoryId;
		$this->hiddenPowerIndex = $hiddenPowerIndex;
	}

	/**
	 * Get the type's id.
	 *
	 * @return TypeId
	 */
	public function id() : TypeId
	{
		return $this->id;
	}

	/**
	 * Get the type's identifier.
	 *
	 * @return string
	 */
	public function identifier() : string
	{
		return $this->identifier;
	}

	/**
	 * Get the type's category id.
	 *
	 * @return CategoryId|null
	 */
	public function categoryId() : ?CategoryId
	{
		return $this->categoryId;
	}

	/**
	 * Get the type's hidden power index.
	 *
	 * @return int|null
	 */
	public function hiddenPowerIndex() : ?int
	{
		return $this->hiddenPowerIndex;
	}
}
