<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Trends\Lines;

use DateTime;

final readonly class TrendPoint
{
	public function __construct(
		private(set) DateTime $date,
		private(set) float $value,
	) {}
}
