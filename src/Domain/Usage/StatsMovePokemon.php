<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Usage;

final readonly class StatsMovePokemon
{
	public function __construct(
		private(set) string $icon,
		private(set) string $identifier,
		private(set) string $name,
		private(set) float $pokemonPercent,
		private(set) float $movePercent,
		private(set) float $usagePercent,
		private(set) float $usageChange,
		private(set) int $baseSpeed,
	) {}
}
