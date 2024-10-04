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
	 * @throws TmNotFoundException if no TM exists with this version group and
	 *     move.
	 */
	public function getByVersionGroupAndMove(
		VersionGroupId $versionGroupId,
		MoveId $moveId,
	) : TechnicalMachine;

	/**
	 * Get TMs in this version group.
	 *
	 * @return TechnicalMachine[] Ordered by machine type, then by number.
	 */
	public function getByVersionGroup(VersionGroupId $versionGroupId) : array;

	/**
	 * Get TMs by their move.
	 *
	 * @return TechnicalMachine[] Indexed by version group id.
	 */
	public function getByMove(MoveId $moveId) : array;

	/**
	 * Get TMs available for this version group, based on all the version groups
	 * that can transfer movesets into this one.
	 *
	 * @return TechnicalMachine[][] Indexed first by version group id and then
	 *     by move id.
	 */
	public function getByIntoVg(VersionGroupId $versionGroupId) : array;
}
