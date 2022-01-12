<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\EggGroups;

use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Versions\GenerationId;

interface PokemonEggGroupRepositoryInterface
{
	/**
	 * Get Pokémon egg groups by Pokémon.
	 *
	 * @return PokemonEggGroup[] Indexed by egg group id.
	 */
	public function getByPokemon(GenerationId $generationId, PokemonId $pokemonId) : array;
}
