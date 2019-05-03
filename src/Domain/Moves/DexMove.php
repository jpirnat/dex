<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Moves;

use Jp\Dex\Domain\Categories\DexCategory;
use Jp\Dex\Domain\Types\DexType;

class DexMove
{
	/** @var string $identifier */
	private $identifier;

	/** @var string $name */
	private $name;

	/** @var DexType $type */
	private $type;

	/** @var DexCategory $category */
	private $category;

	/** @var int $pp */
	private $pp;

	/** @var int $power */
	private $power;

	/** @var int $accuracy */
	private $accuracy;

	/** @var string $description */
	private $description;

	/**
	 * Constructor.
	 *
	 * @param string $identifier
	 * @param string $name
	 * @param DexType $type
	 * @param DexCategory $category
	 * @param int $pp
	 * @param int $power
	 * @param int $accuracy
	 * @param string $description
	 */
	public function __construct(
		string $identifier,
		string $name,
		DexType $type,
		DexCategory $category,
		int $pp,
		int $power,
		int $accuracy,
		string $description
	) {
		$this->identifier = $identifier;
		$this->name = $name;
		$this->type = $type;
		$this->category = $category;
		$this->pp = $pp;
		$this->power = $power;
		$this->accuracy = $accuracy;
		$this->description = $description;
	}

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
