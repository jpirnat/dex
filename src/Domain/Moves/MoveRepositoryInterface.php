<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Moves;

interface MoveRepositoryInterface
{
	/**
	 * Get a move by its id.
	 *
	 * @throws MoveNotFoundException if no move exists with this id.
	 */
	public function getById(MoveId $moveId) : Move;

	/**
	 * Get a move by its identifier.
	 *
	 * @throws MoveNotFoundException if no move exists with this identifier.
	 */
	public function getByIdentifier(string $identifier) : Move;
}
