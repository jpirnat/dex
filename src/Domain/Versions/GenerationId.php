<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Versions;

use Jp\Dex\Domain\EntityId;

final class GenerationId extends EntityId
{
	/** @var int $CURRENT */
	public const CURRENT = 7;
}
