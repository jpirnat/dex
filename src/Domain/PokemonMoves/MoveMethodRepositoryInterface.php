<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\PokemonMoves;

interface MoveMethodRepositoryInterface
{
	/**
	 * Get all move methods.
	 *
	 * @return MoveMethod[] Indexed by id, sorted by sort value.
	 */
	public function getAll() : array;
}
