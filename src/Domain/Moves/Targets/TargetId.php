<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Moves\Targets;

use Jp\Dex\Domain\EntityId;

final class TargetId extends EntityId
{
	private const int ALL_ADJACENT_POKEMON = 4;
	private const int ALL_ADJACENT_OPPONENTS = 5;

	public function hitsMultiplePokemon() : bool
	{
		return $this->value === self::ALL_ADJACENT_POKEMON
			|| $this->value === self::ALL_ADJACENT_OPPONENTS
		;
	}
}
