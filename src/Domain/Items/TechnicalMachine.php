<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Items;

use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Versions\VersionGroupId;

class TechnicalMachine
{
	/** @var VersionGroupId $versionGroupId */
	private $versionGroupId;

	/** @var bool $isHm */
	private $isHm;

	/** @var int $number */
	private $number;

	/** @var ItemId $itemId */
	private $itemId;

	/** @var MoveId $moveId */
	private $moveId;

	/**
	 * Constructor.
	 *
	 * @param VersionGroupId $versionGroupId
	 * @param bool $isHm
	 * @param int $number
	 * @param ItemId $itemId
	 * @param MoveId $moveId
	 */
	public function __construct(
		VersionGroupId $versionGroupId,
		bool $isHm,
		int $number,
		ItemId $itemId,
		MoveId $moveId
	) {
		$this->versionGroupId = $versionGroupId;
		$this->isHm = $isHm;
		$this->number = $number;
		$this->itemId = $itemId;
		$this->moveId = $moveId;
	}

	/**
	 * Get the TM's version group id.
	 *
	 * @return VersionGroupId
	 */
	public function getVersionGroupId() : VersionGroupId
	{
		return $this->versionGroupId;
	}

	/**
	 * Get whether this machine is an HM.
	 *
	 * @return bool
	 */
	public function isHm() : bool
	{
		return $this->isHm;
	}

	/**
	 * Get the TM's number.
	 *
	 * @return int
	 */
	public function getNumber() : int
	{
		return $this->number;
	}

	/**
	 * Get the TM's item id.
	 *
	 * @return ItemId
	 */
	public function getItemId() : ItemId
	{
		return $this->itemId;
	}

	/**
	 * Get the TM's move id.
	 *
	 * @return MoveId
	 */
	public function getMoveId() : MoveId
	{
		return $this->moveId;
	}
}
