<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Import\Structs;

final readonly class LeadUsage
{
	private(set) float $usagePercent;
	private(set) float $rawPercent;

	public function __construct(
		private(set) int $rank,
		private(set) string $showdownPokemonName,
		float $usagePercent,
		private(set) int $raw,
		float $rawPercent,
	) {
		// Clamp usage percent between 0 and 100.
		if ($usagePercent < 0) {
			$usagePercent = 0;
		}
		if ($usagePercent > 100) {
			$usagePercent = 100;
		}
		$this->usagePercent = $usagePercent;

		// Clamp raw percent between 0 and 100.
		if ($rawPercent < 0) {
			$rawPercent = 0;
		}
		if ($rawPercent > 100) {
			$rawPercent = 100;
		}
		$this->rawPercent = $rawPercent;
	}
}
