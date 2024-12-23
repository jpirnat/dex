<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Import\Structs;

final readonly class Spread
{
	private(set) float $percent;

	public function __construct(
		private(set) string $showdownNatureName,
		private(set) int $hp,
		private(set) int $atk,
		private(set) int $def,
		private(set) int $spa,
		private(set) int $spd,
		private(set) int $spe,
		float $percent,
	) {
		// Clamp percent between 0 and 100.
		if ($percent < 0) {
			$percent = 0;
		}
		if ($percent > 100) {
			$percent = 100;
		}
		$this->percent = $percent;
	}
}
