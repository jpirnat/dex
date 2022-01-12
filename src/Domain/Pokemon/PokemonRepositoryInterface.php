<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Pokemon;

interface PokemonRepositoryInterface
{
	/**
	 * Get a Pokémon by its id.
	 *
	 * @throws PokemonNotFoundException if no Pokémon exists with this id.
	 */
	public function getById(PokemonId $pokemonId) : Pokemon;

	/**
	 * Get a Pokémon by its identifier.
	 *
	 * @throws PokemonNotFoundException if no Pokémon exists with this identifier.
	 */
	public function getByIdentifier(string $identifier) : Pokemon;

	/**
	 * Get all Pokémon.
	 *
	 * @return Pokemon[] Indexed by id. Ordered by sort.
	 */
	public function getAll() : array;
}
