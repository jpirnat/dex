<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Moves\Flags;

final readonly class DexMoveFlag
{
	public function __construct(
		private(set) string $identifier,
		private(set) string $name,
		private(set) string $description,
	) {}
}
