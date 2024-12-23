<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Items;

final readonly class StatsPokemonItem
{
	public function __construct(
		private(set) string $icon,
		private(set) string $identifier,
		private(set) string $name,
		private(set) float $percent,
		private(set) float $change,
	) {}
}
