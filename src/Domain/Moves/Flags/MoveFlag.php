<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Moves\Flags;

final readonly class MoveFlag
{
	public function __construct(
		private(set) MoveFlagId $id,
		private(set) string $identifier,
	) {}
}
