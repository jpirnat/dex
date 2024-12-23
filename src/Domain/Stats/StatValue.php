<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats;

final readonly class StatValue
{
	public function __construct(
		private(set) StatId $statId,
		private(set) float $value,
	) {}
}
