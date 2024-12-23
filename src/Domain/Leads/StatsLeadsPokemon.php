<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Leads;

final readonly class StatsLeadsPokemon
{
	public function __construct(
		private(set) int $rank,
		private(set) string $icon,
		private(set) string $identifier,
		private(set) string $name,
		private(set) float $usagePercent,
		private(set) float $usageChange,
		private(set) int $raw,
		private(set) float $rawPercent,
		private(set) int $baseSpeed,
	) {}
}
