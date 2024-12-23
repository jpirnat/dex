<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Moves;

use Jp\Dex\Domain\Categories\DexCategory;
use Jp\Dex\Domain\Types\DexType;

final readonly class DexMove
{
	public function __construct(
		private(set) string $identifier,
		private(set) string $name,
		private(set) DexType $type,
		private(set) DexCategory $category,
		private(set) int $pp,
		private(set) int $power,
		private(set) int $accuracy,
		private(set) string $description,
	) {}
}
