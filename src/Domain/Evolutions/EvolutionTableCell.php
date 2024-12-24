<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Evolutions;

final readonly class EvolutionTableCell
{
	public function __construct(
		private(set) int $rowspan,
		private(set) bool $isFirstStage,
		private(set) string $icon,
		private(set) string $identifier,
		private(set) string $name,
		/** @var EvolutionTableMethod[] $methods */
		private(set) array $methods,
	) {}
}
