<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Moves\Targets;

use Jp\Dex\Domain\EntityId;

final class TargetId extends EntityId
{
	private const ALL_ADJACENT_POKEMON = 4;
	private const ALL_ADJACENT_OPPONENTS = 5;

	public function hitsMultiplePokemon() : bool
	{
		return $this->id === self::ALL_ADJACENT_POKEMON
			|| $this->id === self::ALL_ADJACENT_OPPONENTS
		;
	}
}
