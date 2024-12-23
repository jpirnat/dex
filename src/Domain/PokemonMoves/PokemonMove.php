<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\PokemonMoves;

use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Versions\VersionGroupId;

final readonly class PokemonMove
{
	public function __construct(
		private(set) PokemonId $pokemonId,
		private(set) VersionGroupId $versionGroupId,
		private(set) MoveId $moveId,
		private(set) MoveMethodId $moveMethodId,
		private(set) int $level,
		private(set) int $sort,
	) {}
}
