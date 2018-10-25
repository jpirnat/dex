<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Abilities;

use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Versions\Generation;

interface PokemonAbilityRepositoryInterface
{
	/**
	 * Get a Pokémon's abilities by generation and Pokémon.
	 *
	 * @param Generation $generation
	 * @param PokemonId $pokemonId
	 *
	 * @return PokemonAbility[] Ordered by slot.
	 */
	public function getByGenerationAndPokemon(
		Generation $generation,
		PokemonId $pokemonId
	) : array;

	/**
	 * Get Pokémon abilities by generation and ability.
	 *
	 * @param Generation $generation
	 * @param AbilityId $abilityId
	 *
	 * @return PokemonAbility[]
	 */
	public function getByGenerationAndAbility(
		Generation $generation,
		AbilityId $abilityId
	) : array;
}
