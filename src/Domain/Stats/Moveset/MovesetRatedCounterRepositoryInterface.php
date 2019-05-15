<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Moveset;

interface MovesetRatedCounterRepositoryInterface
{
	/**
	 * Save a moveset rated counter record.
	 *
	 * @param MovesetRatedCounter $movesetRatedCounter
	 *
	 * @return void
	 */
	public function save(MovesetRatedCounter $movesetRatedCounter) : void;
}
