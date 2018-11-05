<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats;

use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Versions\GenerationId;

interface BaseStatRepositoryInterface
{
	/**
	 * Get a Pokémon's base stats by generation and Pokémon.
	 *
	 * @param GenerationId $generationId
	 * @param PokemonId $pokemonId
	 *
	 * @return StatValueContainer
	 */
	public function getByGenerationAndPokemon(
		GenerationId $generationId,
		PokemonId $pokemonId
	) : StatValueContainer;
}
