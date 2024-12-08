<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\AdvancedPokemonSearch;

use Jp\Dex\Domain\Abilities\AbilityId;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Pokemon\DexPokemon;
use Jp\Dex\Domain\Versions\VersionGroupId;

interface AdvancedPokemonSearchQueriesInterface
{
	/**
	 * Get all move ids, indexed by identifier.
	 *
	 * @return MoveId[] Indexed by identifier.
	 */
	public function getMoveIdentifiersToIds() : array;

	/**
	 * Get dex Pokémon for this advanced search.
	 *
	 * @param MoveId[] $moveIds
	 *
	 * @return DexPokemon[] Indexed by Pokémon id. Ordered by Pokémon sort value.
	 */
	public function search(
		VersionGroupId $versionGroupId,
		?AbilityId $abilityId,
		array $moveIds,
		bool $includeTransferMoves,
		LanguageId $languageId,
	) : array;
}
