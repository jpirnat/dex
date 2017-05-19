<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Pokemon;

use Exception;

interface PokemonRepositoryInterface
{
	/**
	 * Get a Pokémon by its id.
	 *
	 * @param PokemonId $pokemonId
	 *
	 * @throws Exception if no Pokémon exists with this id.
	 *
	 * @return Pokemon
	 */
	public function getById(PokemonId $pokemonId) : Pokemon;

	/**
	 * Get a Pokémon by its identifier.
	 *
	 * @param string $identifier
	 *
	 * @throws Exception if no Pokémon exists with this identifier.
	 *
	 * @return Pokemon
	 */
	public function getByIdentifier(string $identifier) : Pokemon;

	/**
	 * Get all Pokémon. Indexed by Pokémon id.
	 *
	 * @return Pokemon[]
	 */
	public function getAll() : array;
}
