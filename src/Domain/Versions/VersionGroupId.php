<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Versions;

use Jp\Dex\Domain\EntityId;

final class VersionGroupId extends EntityId
{
	public const RED_GREEN = 1;
	public const BLUE = 2;
	public const RED_BLUE = 3;
	public const YELLOW = 4;
}
