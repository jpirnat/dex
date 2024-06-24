<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Pokemon;

use Jp\Dex\Domain\Languages\LanguageId;

interface PokemonNameRepositoryInterface
{
	/**
	 * Get a Pokémon name by language and Pokémon.
	 *
	 * @throws PokemonNameNotFoundException if no Pokémon name exists for this
	 *     language and Pokémon.
	 */
	public function getByLanguageAndPokemon(
		LanguageId $languageId,
		PokemonId $pokemonId,
	) : PokemonName;

	/**
	 * Get Pokémon names by language.
	 *
	 * @return PokemonName[] Indexed by Pokémon id.
	 */
	public function getByLanguage(LanguageId $languageId) : array;
}
