<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\ExperienceGroups;

final readonly class DexExperienceGroup
{
	public function __construct(
		private(set) string $identifier,
		private(set) string $name,
		private(set) int $points,
	) {}
}
