<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Moveset;

interface MovesetRatedCounterRepositoryInterface
{
	/**
	 * Save a moveset rated counter record.
	 */
	public function save(MovesetRatedCounter $movesetRatedCounter) : void;
}
