<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Evolutions;

final readonly class EvolutionTree
{
	public function __construct(
		private(set) bool $isFirstStage,
		private(set) string $icon,
		private(set) string $identifier,
		private(set) string $name,
		/** @var EvolutionTableMethod[] */
		private(set) array $methods,
		/** @var self[] */
		private(set) array $evolutions,
	) {}

	public function countBranches() : int
	{
		if (!$this->evolutions) {
			return 1;
		}

		$branches = 0;

		foreach ($this->evolutions as $evolution) {
			$branches += $evolution->countBranches();
		}

		return $branches;
	}
}
