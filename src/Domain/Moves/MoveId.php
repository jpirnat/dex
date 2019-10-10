<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Moves;

use Jp\Dex\Domain\EntityId;

final class MoveId extends EntityId
{
	/** @var int $SKETCH */
	public const SKETCH = 166;

	/** @var int $TYPED_HIDDEN_POWER_BEGIN */
	public const TYPED_HIDDEN_POWER_BEGIN = 10001;

	/** @var int $TYPED_HIDDEN_POWER_END */
	public const TYPED_HIDDEN_POWER_END = 10016;
}
