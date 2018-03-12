<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Moves;

use Jp\Dex\Domain\Versions\Generation;

interface GenerationMoveRepositoryInterface
{
	/**
	 * Get a generation move by its generation and move.
	 *
	 * @param Generation $generation
	 * @param MoveId $moveId
	 *
	 * @throws GenerationMoveNotFoundException if no generation move exists with
	 *     this generation and move.
	 *
	 * @return GenerationMove
	 */
	public function getByGenerationAndMove(
		Generation $generation,
		MoveId $moveId
	) : GenerationMove;
}
