<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Items;

use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Versions\GenerationId;
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

	/**
	 * Get TMs by their move.
	 *
	 * @param MoveId $moveId
	 *
	 * @return TechnicalMachine[] Indexed by version group id.
	 */
	public function getByMove(MoveId $moveId) : array;

	/**
	 * Get TMs between these two generations, inclusive. This method is used to
	 * get all potentially relevant TMs for the dex Pokémon page.
	 *
	 * @param GenerationId $begin
	 * @param GenerationId $end
	 *
	 * @return TechnicalMachine[][] Indexed first by version group id and then
	 *     by move id.
	 */
	public function getBetween(GenerationId $begin, GenerationId $end) : array;
}
