<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Items;

use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Versions\VersionGroupId;

final readonly class TechnicalMachine
{
	public function __construct(
		private(set) VersionGroupId $versionGroupId,
		private(set) MachineType $machineType,
		private(set) int $number,
		private(set) ItemId $itemId,
		private(set) MoveId $moveId,
	) {}
}
