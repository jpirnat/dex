<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Versions;

use Jp\Dex\Domain\Pokemon\PokemonId;

interface GenerationRepositoryInterface
{
	/**
	 * Get a generation by its id.
	 *
	 * @param GenerationId $generationId
	 *
	 * @throws GenerationNotFoundException if no generation exists with this id.
	 *
	 * @return Generation
	 */
	public function getById(GenerationId $generationId) : Generation;

	/**
	 * Get a generation by its identifier
	 *
	 * @param string $identifier
	 *
	 * @throws GenerationNotFoundException if no generation exists with this
	 *     identifier.
	 *
	 * @return Generation
	 */
	public function getByIdentifier(string $identifier) : Generation;

	/**
	 * Get generations that this Pokémon has appeared in (via version groups).
	 *
	 * @param PokemonId $pokemonId
	 *
	 * @return Generation[] Indexed by id. Ordered by id.
	 */
	public function getWithPokemon(PokemonId $pokemonId) : array;

	/**
	 * Get generations since the given generation, inclusive.
	 *
	 * @param GenerationId $generationId
	 *
	 * @return Generation[] Indexed by id. Ordered by id.
	 */
	public function getSince(GenerationId $generationId) : array;
}
