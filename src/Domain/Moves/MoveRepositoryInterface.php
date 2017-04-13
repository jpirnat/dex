<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Moves;

use Exception;

interface MoveRepositoryInterface
{
	/**
	 * Get a move by its identifier.
	 *
	 * @param string $identifier
	 *
	 * @throws Exception if no move exists with this identifier.
	 *
	 * @return Move
	 */
	public function getByIdentifier(string $identifier) : Move;
}
