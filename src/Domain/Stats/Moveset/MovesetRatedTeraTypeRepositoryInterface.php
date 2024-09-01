<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Moveset;

interface MovesetRatedTeraTypeRepositoryInterface
{
	/**
	 * Save a moveset rated tera type record.
	 */
	public function save(MovesetRatedTeraType $movesetRatedTeraType) : void;
}
