<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Moves;

use Jp\Dex\Domain\EntityId;

final class MoveId extends EntityId
{
	public const int SKETCH = 166;
	public const int HIDDEN_POWER = 237;
	public const int FLYING_PRESS = 560;
	public const int FREEZE_DRY = 573;
	public const int THOUSAND_ARROWS = 614;
	public const int PSYSHIELD_BASH = 828;
	public const int BARB_BARRAGE = 839;
	public const int TYPED_HIDDEN_POWER_BEGIN = 10001;
	public const int TYPED_HIDDEN_POWER_END = 10016;
}
