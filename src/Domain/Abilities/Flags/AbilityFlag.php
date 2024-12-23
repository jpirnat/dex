<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Abilities\Flags;

final readonly class AbilityFlag
{
	public function __construct(
		private(set) AbilityFlagId $id,
		private(set) string $identifier,
	) {}
}
