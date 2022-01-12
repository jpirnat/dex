<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Moveset;

interface MovesetRatedSpreadRepositoryInterface
{
	/**
	 * Save a moveset rated spread record.
	 */
	public function save(MovesetRatedSpread $movesetRatedSpread) : void;
}
