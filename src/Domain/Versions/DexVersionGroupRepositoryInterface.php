<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Versions;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Pokemon\PokemonId;

interface DexVersionGroupRepositoryInterface
{
	/**
	 * Get a dex version group by its id.
	 *
	 * @throws VersionGroupNotFoundException if no version group exists with
	 */
	public function getById(
		VersionGroupId $versionGroupId,
		LanguageId $languageId,
	) : DexVersionGroup;

	/**
	 * Get dex version groups that this Pokémon has appeared in, up to a certain
	 * generation. This method is used to get all relevant version groups for
	 * the dex Pokémon page.
	 *
	 * @return DexVersionGroup[] Indexed by id. Ordered by sort.
	 */
	public function getWithPokemon(
		PokemonId $pokemonId,
		LanguageId $languageId,
		GenerationId $end,
	) : array;

	/**
	 * Get dex version groups that this move has appeared in, up to a certain
	 * generation. This method is used to get all relevant version groups for
	 * the dex move page.
	 *
	 * @return DexVersionGroup[] Indexed by id. Ordered by sort.
	 */
	public function getWithMove(
		MoveId $moveId,
		LanguageId $languageId,
		GenerationId $end,
	) : array;
}
