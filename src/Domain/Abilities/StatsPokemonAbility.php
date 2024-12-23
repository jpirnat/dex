<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Abilities;

final readonly class StatsPokemonAbility
{
	public function __construct(
		private(set) string $identifier,
		private(set) string $name,
		private(set) float $percent,
		private(set) float $change,
	) {}
}
