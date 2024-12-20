<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\PokemonMoves;

use Jp\Dex\Domain\EntityId;

final class MoveMethodId extends EntityId
{
	public const int LEVEL_UP = 1;
	public const int MACHINE = 2;
	public const int EGG = 3;
	public const int SKETCH = 4;
	public const int TUTOR = 5;
	public const int LIGHT_BALL = 6;
	public const int FORM_CHANGE = 7;
	public const int EVOLUTION = 8;
	public const int REMINDER = 10;
	public const int SHADOW = 101;
	public const int PURIFICATION = 102;
}
