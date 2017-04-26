<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Pokemon;

use Exception;
use Jp\Dex\Domain\Languages\LanguageId;

interface PokemonNameRepositoryInterface
{
	/**
	 * Get a Pokémon name by language and Pokémon.
	 *
	 * @param LanguageId $languageId
	 * @param PokemonId $pokemonId
	 *
	 * @throws Exception if no name exists.
	 *
	 * @return PokemonName
	 */
	public function getByLanguageAndPokemon(
		LanguageId $languageId,
		PokemonId $pokemonId
	) : PokemonName;
}
