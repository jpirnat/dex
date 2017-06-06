<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Moves;

interface MoveRepositoryInterface
{
	/**
	 * Get a move by its identifier.
	 *
	 * @param string $identifier
	 *
	 * @throws MoveNotFoundException if no move exists with this identifier.
	 *
	 * @return Move
	 */
	public function getByIdentifier(string $identifier) : Move;
}
