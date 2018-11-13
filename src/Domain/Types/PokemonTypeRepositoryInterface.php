<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Types;

use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Versions\GenerationId;

interface PokemonTypeRepositoryInterface
{
	/**
	 * Get Pokémon's types by generation and Pokémon.
	 *
	 * @param GenerationId $generationId
	 * @param PokemonId $pokemonId
	 *
	 * @return PokemonType[] Indexed and ordered by slot.
	 */
	public function getByGenerationAndPokemon(
		GenerationId $generationId,
		PokemonId $pokemonId
	) : array;

	/**
	 * Get Pokémon's types by generation and type.
	 *
	 * @param GenerationId $generationId
	 * @param TypeId $typeId
	 *
	 * @return PokemonType[] Indexed by Pokémon id.
	 */
	public function getByGenerationAndType(
		GenerationId $generationId,
		TypeId $typeId
	) : array;
}
