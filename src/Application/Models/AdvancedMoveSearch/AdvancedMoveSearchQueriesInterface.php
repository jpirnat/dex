<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\AdvancedMoveSearch;

use Jp\Dex\Domain\Categories\CategoryId;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Moves\DexMove;
use Jp\Dex\Domain\Moves\Flags\MoveFlagId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Types\TypeId;
use Jp\Dex\Domain\Versions\VersionGroupId;

interface AdvancedMoveSearchQueriesInterface
{
	/**
	 * Get dex moves for this advanced search.
	 *
	 * @param TypeId[] $typeIds
	 * @param CategoryId[] $categoryIds
	 * @param MoveFlagId[] $yesFlagIds
	 * @param MoveFlagId[] $noFlagIds
	 *
	 * @return DexMove[] Indexed by move id. Ordered by name.
	 */
	public function search(
		VersionGroupId $versionGroupId,
		array $typeIds,
		array $categoryIds,
		array $yesFlagIds,
		array $noFlagIds,
		?PokemonId $pokemonId,
		bool $includeTransferMoves,
		LanguageId $languageId,
	) : array;
}
