<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Abilities;

final readonly class Ability
{
	public function __construct(
		private(set) AbilityId $id,
		private(set) string $identifier,
	) {}
}
