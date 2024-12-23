<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\EggGroups;

final readonly class DexEggGroup
{
	public function __construct(
		private(set) string $identifier,
		private(set) string $name,
	) {}
}
