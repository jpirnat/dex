<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Abilities\Flags;

final readonly class DexAbilityFlag
{
	public function __construct(
		private(set) string $identifier,
		private(set) string $name,
		private(set) string $description,
	) {}
}
