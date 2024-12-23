<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Import\Structs;

final readonly class Counter1
{
	public function __construct(
		private(set) string $showdownPokemonName,
		private(set) float $number1,
		private(set) float $number2,
		private(set) float $number3,
	) {}
}
