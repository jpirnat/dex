<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\PokemonMoves;

final readonly class MoveMethod
{
	public function __construct(
		private(set) MoveMethodId $id,
		private(set) string $identifier,
		private(set) int $sort,
	) {}
}
