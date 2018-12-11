<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Items;

use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Versions\VersionGroupId;

interface TmRepositoryInterface
{
	/**
	 * Get a TM by its version group and move.
	 *
	 * @param VersionGroupId $versionGroupId
	 * @param MoveId $moveId
	 *
	 * @throws TmNotFoundException if no TM exists with this version group and
	 *     move.
	 *
	 * @return TechnicalMachine
	 */
	public function getByVersionGroupAndMove(
		VersionGroupId $versionGroupId,
		MoveId $moveId
	) : TechnicalMachine;
}
