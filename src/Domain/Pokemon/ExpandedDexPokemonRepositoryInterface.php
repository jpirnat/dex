<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Pokemon;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Versions\VersionGroupId;

interface ExpandedDexPokemonRepositoryInterface
{
	/**
	 * Get an expanded dex Pokémon by its id.
	 *
	 * @throws VgPokemonNotFoundException if no Pokémon exists with this id.
	 */
	public function getById(
		VersionGroupId $versionGroupId,
		PokemonId $pokemonId,
		LanguageId $languageId,
	) : ExpandedDexPokemon;
}
