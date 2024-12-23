<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Moves;

use Jp\Dex\Domain\Categories\DexCategory;
use Jp\Dex\Domain\Moves\Targets\TargetId;
use Jp\Dex\Domain\Types\DexType;

final readonly class StatsPokemonMove
{
	public function __construct(
		private(set) string $identifier,
		private(set) string $name,
		private(set) float $percent,
		private(set) float $change,
		private(set) DexType $type,
		private(set) DexCategory $category,
		private(set) int $pp,
		private(set) int $power,
		private(set) int $accuracy,
		private(set) int $priority,
		private(set) TargetId $targetId,
	) {}
}
