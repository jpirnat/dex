<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Counters;

final readonly class StatsPokemonCounter
{
	public function __construct(
		private(set) string $icon,
		private(set) string $identifier,
		private(set) string $name,
		private(set) float $score,
		private(set) float $percent,
		private(set) float $standardDeviation,
		private(set) float $percentKnockedOut,
		private(set) float $percentSwitchedOut,
	) {}
}
