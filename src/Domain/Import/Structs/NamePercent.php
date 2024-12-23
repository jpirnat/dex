<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Import\Structs;

final readonly class NamePercent
{
	private(set) float $percent;

	public function __construct(
		private(set) string $showdownName,
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
