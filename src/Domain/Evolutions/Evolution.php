<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Evolutions;

use Jp\Dex\Domain\Forms\FormId;
use Jp\Dex\Domain\Items\ItemId;
use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Types\TypeId;
use Jp\Dex\Domain\Versions\VersionGroupId;
use Jp\Dex\Domain\Versions\VersionId;

final readonly class Evolution
{
	public function __construct(
		private(set) VersionGroupId $versionGroupId,
		private(set) FormId $evoFromId,
		private(set) EvoMethodId $evoMethodId,
		private(set) FormId $evoIntoId,
		private(set) int $level,
		private(set) ?ItemId $itemId,
		private(set) ?MoveId $moveId,
		private(set) ?PokemonId $pokemonId,
		private(set) ?TypeId $typeId,
		private(set) ?VersionId $versionId,
		private(set) int $otherParameter,
	) {}
}
