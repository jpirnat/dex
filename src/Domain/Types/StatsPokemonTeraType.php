<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Types;

final readonly class StatsPokemonTeraType
{
	public function __construct(
		private(set) string $icon,
		private(set) string $identifier,
		private(set) string $name,
		private(set) float $percent,
		private(set) float $change,
	) {}
}
