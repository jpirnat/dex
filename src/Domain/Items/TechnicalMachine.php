<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Items;

use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Versions\VersionGroupId;

final class TechnicalMachine
{
	public function __construct(
		private VersionGroupId $versionGroupId,
		private MachineType $machineType,
		private int $number,
		private ItemId $itemId,
		private MoveId $moveId,
	) {}

	/**
	 * Get the TM's version group id.
	 */
	public function getVersionGroupId() : VersionGroupId
	{
		return $this->versionGroupId;
	}

	/**
	 * Get the TM's machine type.
	 */
	public function getMachineType() : MachineType
	{
		return $this->machineType;
	}

	/**
	 * Get the TM's number.
	 */
	public function getNumber() : int
	{
		return $this->number;
	}

	/**
	 * Get the TM's item id.
	 */
	public function getItemId() : ItemId
	{
		return $this->itemId;
	}

	/**
	 * Get the TM's move id.
	 */
	public function getMoveId() : MoveId
	{
		return $this->moveId;
	}
}
