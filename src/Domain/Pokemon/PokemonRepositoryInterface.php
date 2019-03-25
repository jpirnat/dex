<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Pokemon;

interface PokemonRepositoryInterface
{
	/**
	 * Get a Pokémon by its id.
	 *
	 * @param PokemonId $pokemonId
	 *
	 * @throws PokemonNotFoundException if no Pokémon exists with this id.
	 *
	 * @return Pokemon
	 */
	public function getById(PokemonId $pokemonId) : Pokemon;

	/**
	 * Get a Pokémon by its identifier.
	 *
	 * @param string $identifier
	 *
	 * @throws PokemonNotFoundException if no Pokémon exists with this
	 *     identifier.
	 *
	 * @return Pokemon
	 */
	public function getByIdentifier(string $identifier) : Pokemon;

	/**
	 * Get all Pokémon.
	 *
	 * @return Pokemon[] Indexed by id. Ordered by sort.
	 */
	public function getAll() : array;

	/**
	 * Get other Pokémon in the same transformation group as this Pokémon.
	 *
	 * @param PokemonId $pokemonId
	 *
	 * @return Pokemon[] Indexed by id.
	 */
	public function getTransformationsOf(PokemonId $pokemonId) : array;
}
