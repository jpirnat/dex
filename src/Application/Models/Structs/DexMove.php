<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\Structs;

class DexMove
{
	/** @var string $moveIdentifier */
	private $moveIdentifier;

	/** @var string $moveName */
	private $moveName;

	/** @var DexType $type */
	private $type;

	/** @var string $categoryIcon */
	private $categoryIcon;

	/** @var int $pp */
	private $pp;

	/** @var int $power */
	private $power;

	/** @var int $accuracy */
	private $accuracy;

	/** @var int $priority */
	private $priority;

	/** @var string $moveDescription */
	private $moveDescription;

	/**
	 * Constructor.
	 *
	 * @param string $moveIdentifier
	 * @param string $moveName
	 * @param DexType $type
	 * @param string $categoryIcon
	 * @param int $pp
	 * @param int $power
	 * @param int $accuracy
	 * @param int $priority
	 * @param string $moveDescription
	 */
	public function __construct(
		string $moveIdentifier,
		string $moveName,
		DexType $type,
		string $categoryIcon,
		int $pp,
		int $power,
		int $accuracy,
		int $priority,
		string $moveDescription
	) {
		$this->moveIdentifier = $moveIdentifier;
		$this->moveName = $moveName;
		$this->type = $type;
		$this->categoryIcon = $categoryIcon;
		$this->pp = $pp;
		$this->power = $power;
		$this->accuracy = $accuracy;
		$this->priority = $priority;
		$this->moveDescription = $moveDescription;
	}

	/**
	 * Get the move identifier.
	 *
	 * @return string
	 */
	public function getMoveIdentifier() : string
	{
		return $this->moveIdentifier;
	}

	/**
	 * Get the move name.
	 *
	 * @return string
	 */
	public function getMoveName() : string
	{
		return $this->moveName;
	}

	/**
	 * Get the type.
	 *
	 * @return DexType
	 */
	public function getType() : DexType
	{
		return $this->type;
	}

	/**
	 * Get the category icon.
	 *
	 * @return string
	 */
	public function getCategoryIcon() : string
	{
		return $this->categoryIcon;
	}

	/**
	 * Get the PP.
	 *
	 * @return int
	 */
	public function getPP() : int
	{
		return $this->pp;
	}

	/**
	 * Get the power.
	 *
	 * @return int
	 */
	public function getPower() : int
	{
		return $this->power;
	}

	/**
	 * Get the accuracy.
	 *
	 * @return int
	 */
	public function getAccuracy() : int
	{
		return $this->accuracy;
	}

	/**
	 * Get the priority.
	 *
	 * @return int
	 */
	public function getPriority() : int
	{
		return $this->priority;
	}

	/**
	 * Get the move description.
	 *
	 * @return string
	 */
	public function getMoveDescription() : string
	{
		return $this->moveDescription;
	}
}
