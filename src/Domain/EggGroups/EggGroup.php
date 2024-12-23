<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\EggGroups;

final readonly class EggGroup
{
	public function __construct(
		private(set) EggGroupId $id,
		private(set) string $identifier,
	) {}
}
