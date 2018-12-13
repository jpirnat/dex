<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\EggGroups;

use Jp\Dex\Domain\Pokemon\PokemonId;

interface PokemonEggGroupRepositoryInterface
{
	/**
	 * Get Pokémon egg groups by Pokémon.
	 *
	 * @param PokemonId $pokemonId
	 *
	 * @return PokemonEggGroup[] Indexed by egg group id.
	 */
	public function getByPokemon(PokemonId $pokemonId) : array;
}
