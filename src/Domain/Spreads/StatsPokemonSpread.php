<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Spreads;

use Jp\Dex\Domain\Stats\StatId;
use Jp\Dex\Domain\Stats\StatValueContainer;

final readonly class StatsPokemonSpread
{
	public function __construct(
		private(set) string $natureName,
		private(set) ?StatId $increasedStatId,
		private(set) ?StatId $decreasedStatId,
		private(set) StatValueContainer $evs,
		private(set) float $percent,
	) {}
}
