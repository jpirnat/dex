<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Calculators;

use Jp\Dex\Domain\Versions\GenerationId;
use Jp\Dex\Domain\Versions\GenerationNotFoundException;

final readonly class EggCycleCalculator
{
	/**
	 * Calculate the number of steps it would take for an egg to hatch.
	 *
	 * @param string $oPower '', 'weak', 'medium', or 'strong'.
	 *
	 * @throws GenerationNotFoundException if steps to hatch cannot be calculated
	 *     for $generationId.
	 */
	public function calculateStepsToHatch(
		GenerationId $generationId,
		int $baseEggCycles,
		bool $magmaArmor,
		string $oPower,
	) : int {
		$generation = $generationId->value();

		$stepsPerCycle = [
			2 => 256,
			3 => 256,
			4 => 255,
			5 => 257,
			6 => 257,
			7 => 257,
		][$generation];

		$magmaArmor = $magmaArmor ? 2 : 1;

		if ($oPower && 5 <= $generation && $generation <= 7) {
			$stepsPerCycle = [
				'weak' => 205,
				'medium' => 171,
				'strong' => 129,
			][$oPower];
		}

		if ($generation === 2) {
			return $baseEggCycles * $stepsPerCycle;
		}
		if ($generation === 3) {
			return (ceil($baseEggCycles / $magmaArmor) + 1) * $stepsPerCycle - 1;
		}
		if ($generation === 4) {
			return (ceil($baseEggCycles / $magmaArmor) + 1) * $stepsPerCycle;
		}
		if ($generation === 5) {
			return ceil($baseEggCycles / $magmaArmor) * $stepsPerCycle;
		}
		if ($generation === 6) {
			return ceil($baseEggCycles / $magmaArmor) * $stepsPerCycle;
		}
		if ($generation === 7) {
			return ceil($baseEggCycles / $magmaArmor) * $stepsPerCycle;
		}

		throw new GenerationNotFoundException(
			"Cannot calculate steps to hatch for generation id $generation."
		);
	}
}
