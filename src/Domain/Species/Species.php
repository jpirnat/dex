<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Species;

final readonly class Species
{
	public function __construct(
		private(set) SpeciesId $id,
		private(set) string $identifier,
		private(set) int $eggCycles,
	) {}
}
