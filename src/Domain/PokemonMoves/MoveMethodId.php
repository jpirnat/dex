<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\PokemonMoves;

use Jp\Dex\Domain\EntityId;

final class MoveMethodId extends EntityId
{
	public const LEVEL_UP = 1;
	public const MACHINE = 2;
	public const EGG = 3;
	public const SKETCH = 4;
	public const TUTOR = 5;
	public const LIGHT_BALL = 6;
	public const FORM_CHANGE = 7;
	public const EVOLUTION = 8;
	public const SHADOW = 101;
	public const PURIFICATION = 102;
}
