<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Versions;

use Jp\Dex\Domain\EntityId;

class VersionGroupId extends EntityId
{
	/** @var int $RED_GREEN */
	public const RED_GREEN = 1;

	/** @var int $BLUE */
	public const BLUE = 2;

	/** @var int $RED_BLUE */
	public const RED_BLUE = 3;
}
