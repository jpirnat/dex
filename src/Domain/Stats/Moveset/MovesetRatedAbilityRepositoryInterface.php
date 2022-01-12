<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Moveset;

interface MovesetRatedAbilityRepositoryInterface
{
	/**
	 * Save a moveset rated ability record.
	 */
	public function save(MovesetRatedAbility $movesetRatedAbility) : void;
}
