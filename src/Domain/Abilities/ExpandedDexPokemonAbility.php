<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Abilities;

final readonly class ExpandedDexPokemonAbility
{
	public function __construct(
		private(set) string $identifier,
		private(set) string $name,
		private(set) string $description,
		private(set) bool $isHiddenAbility,
	) {}
}
