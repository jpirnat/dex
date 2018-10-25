<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Types;

use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Versions\Generation;

interface PokemonTypeRepositoryInterface
{
	/**
	 * Get Pokémon's types by generation and Pokémon.
	 *
	 * @param Generation $generation
	 * @param PokemonId $pokemonId
	 *
	 * @return PokemonType[] Indexed and ordered by slot.
	 */
	public function getByGenerationAndPokemon(
		Generation $generation,
		PokemonId $pokemonId
	) : array;
}
