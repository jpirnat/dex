<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Moves;

use Jp\Dex\Domain\Categories\DexCategory;
use Jp\Dex\Domain\Types\DexType;

final class DexMove
{
	public function __construct(
		private string $identifier,
		private string $name,
		private DexType $type,
		private DexCategory $category,
		private int $pp,
		private int $power,
		private int $accuracy,
		private string $description,
	) {}

	/**
	 * Get the move's identifier.
	 *
	 * @return string
	 */
	public function getIdentifier() : string
	{
		return $this->identifier;
	}

	/**
	 * Get the move's name.
	 *
	 * @return string
	 */
	public function getName() : string
	{
		return $this->name;
	}

	/**
	 * Get the move's type.
	 *
	 * @return DexType
	 */
	public function getType() : DexType
	{
		return $this->type;
	}

	/**
	 * Get the move's category.
	 *
	 * @return DexCategory
	 */
	public function getCategory() : DexCategory
	{
		return $this->category;
	}

	/**
	 * Get the move's PP.
	 *
	 * @return int
	 */
	public function getPP() : int
	{
		return $this->pp;
	}

	/**
	 * Get the move's power.
	 *
	 * @return int
	 */
	public function getPower() : int
	{
		return $this->power;
	}

	/**
	 * Get the move's accuracy.
	 *
	 * @return int
	 */
	public function getAccuracy() : int
	{
		return $this->accuracy;
	}

	/**
	 * Get the move's description.
	 *
	 * @return string
	 */
	public function getDescription() : string
	{
		return $this->description;
	}
}
