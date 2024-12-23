<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Leads;

final readonly class AveragedLeadsPokemon
{
	public function __construct(
		private(set) int $rank,
		private(set) string $icon,
		private(set) int $numberOfMonths,
		private(set) string $identifier,
		private(set) string $name,
		private(set) float $usagePercent,
		private(set) int $raw,
		private(set) float $rawPercent,
	) {}
}
