<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Moveset;

interface MovesetRatedItemRepositoryInterface
{
	/**
	 * Save a moveset rated item record.
	 */
	public function save(MovesetRatedItem $movesetRatedItem) : void;
}
