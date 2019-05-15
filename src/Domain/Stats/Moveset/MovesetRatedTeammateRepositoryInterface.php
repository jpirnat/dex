<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Moveset;

interface MovesetRatedTeammateRepositoryInterface
{
	/**
	 * Save a moveset rated teammate record.
	 *
	 * @param MovesetRatedTeammate $movesetRatedTeammate
	 *
	 * @return void
	 */
	public function save(MovesetRatedTeammate $movesetRatedTeammate) : void;
}
