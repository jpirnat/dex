<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Types;

use Jp\Dex\Domain\Versions\GenerationId;

final readonly class TypeMatchup
{
	public function __construct(
		private(set) GenerationId $generationId,
		private(set) string $attackingTypeIdentifier,
		private(set) string $defendingTypeIdentifier,
		private(set) float $multiplier,
	) {}
}
