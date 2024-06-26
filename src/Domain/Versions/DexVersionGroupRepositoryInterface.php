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
	 * Get dex version groups that this Pokémon has appeared in, and that can
	 * transfer movesets into this version group.
	 *
	 * @return DexVersionGroup[] Indexed by id. Ordered by sort.
	 */
	public function getByIntoVgWithPokemon(
		VersionGroupId $versionGroupId,
		PokemonId $pokemonId,
		LanguageId $languageId,
	) : array;

	/**
	 * Get dex version groups that this move has appeared in, up to a certain
	 * generation. This method is used to get all relevant version groups for
	 * the dex move page.
	 *
	 * @return DexVersionGroup[] Indexed by id. Ordered by sort.
	 */
	public function getByIntoVgWithMove(
		VersionGroupId $versionGroupId,
		MoveId $moveId,
		LanguageId $languageId,
	) : array;
}
