<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Evolutions;

final readonly class EvolutionTableMethod
{
	public function __construct(
		private(set) string $html,
	) {}
}
