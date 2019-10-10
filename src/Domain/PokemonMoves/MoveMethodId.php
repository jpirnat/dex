<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\PokemonMoves;

use Jp\Dex\Domain\EntityId;

final class MoveMethodId extends EntityId
{
	/** @var int $LEVEL_UP */
	public const LEVEL_UP = 1;

	/** @var int $MACHINE */
	public const MACHINE = 2;

	/** @var int $EGG */
	public const EGG = 3;

	/** @var int $SKETCH */
	public const SKETCH = 4;

	/** @var int $TUTOR */
	public const TUTOR = 5;

	/** @var int $LIGHT_BALL */
	public const LIGHT_BALL = 6;

	/** @var int $FORM_CHANGE */
	public const FORM_CHANGE = 7;

	/** @var int $EVOLUTION */
	public const EVOLUTION = 8;

	/** @var int $SHADOW */
	public const SHADOW = 101;

	/** @var int $PURIFICATION */
	public const PURIFICATION = 102;
}
