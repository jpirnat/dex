<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Items;

use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Versions\VersionGroupId;

final class TechnicalMachine
{
	private VersionGroupId $versionGroupId;
	private MachineType $machineType;
	private int $number;
	private ItemId $itemId;
	private MoveId $moveId;

	/**
	 * Constructor.
	 *
	 * @param VersionGroupId $versionGroupId
	 * @param MachineType $machineType
	 * @param int $number
	 * @param ItemId $itemId
	 * @param MoveId $moveId
	 */
	public function __construct(
		VersionGroupId $versionGroupId,
		MachineType $machineType,
		int $number,
		ItemId $itemId,
		MoveId $moveId
	) {
		$this->versionGroupId = $versionGroupId;
		$this->machineType = $machineType;
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
	 * Get the TM's machine type.
	 *
	 * @return MachineType
	 */
	public function getMachineType() : MachineType
	{
		return $this->machineType;
	}

	/**
	 * Get whether this machine is an HM.
	 *
	 * @return bool
	 */
	public function isHm() : bool
	{
		return $this->machineType->value() === MachineType::HM;
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
