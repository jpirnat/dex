<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Abilities;

use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Versions\GenerationId;

interface PokemonAbilityRepositoryInterface
{
	/**
	 * Get a Pokémon's abilities by generation and Pokémon.
	 *
	 * @param GenerationId $generationId
	 * @param PokemonId $pokemonId
	 *
	 * @return PokemonAbility[] Ordered by slot.
	 */
	public function getByGenerationAndPokemon(
		GenerationId $generationId,
		PokemonId $pokemonId
	) : array;

	/**
	 * Get Pokémon abilities by generation and ability.
	 *
	 * @param GenerationId $generationId
	 * @param AbilityId $abilityId
	 *
	 * @return PokemonAbility[]
	 */
	public function getByGenerationAndAbility(
		GenerationId $generationId,
		AbilityId $abilityId
	) : array;
}
