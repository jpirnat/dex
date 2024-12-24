<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Types;

use Jp\Dex\Domain\Categories\CategoryId;

final readonly class Type
{
	public function __construct(
		private(set) TypeId $id,
		private(set) string $identifier,
		private(set) ?CategoryId $categoryId,
		private(set) string $symbolIcon,
		private(set) ?int $hiddenPowerIndex,
		private(set) string $colorCode, // "#rrggbb"
	) {}
}
