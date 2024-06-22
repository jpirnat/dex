<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Moves;

use Jp\Dex\Domain\EntityId;

final class MoveId extends EntityId
{
	public const SKETCH = 166;
	public const HIDDEN_POWER = 237;
	public const FLYING_PRESS = 560;
	public const FREEZE_DRY = 573;
	public const THOUSAND_ARROWS = 614;
	public const TYPED_HIDDEN_POWER_BEGIN = 10001;
	public const TYPED_HIDDEN_POWER_END = 10016;
}
