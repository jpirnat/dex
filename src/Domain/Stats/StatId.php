<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats;

use Jp\Dex\Domain\EntityId;

class StatId extends EntityId
{
	/** @var int $HP */
	public const HP = 1;

	/** @var int $ATTACK */
	public const ATTACK = 2;

	/** @var int $DEFENSE */
	public const DEFENSE = 3;

	/** @var int $SPEED */
	public const SPEED = 4;

	/** @var int SPECIAL */
	public const SPECIAL = 5;

	/** @var int $SPECIAL_ATTACK */
	public const SPECIAL_ATTACK = 8;

	/** @var int $SPECIAL_DEFENSE */
	public const SPECIAL_DEFENSE = 9;
}
