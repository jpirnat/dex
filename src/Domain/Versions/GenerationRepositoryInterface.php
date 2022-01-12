<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Versions;

use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Pokemon\PokemonId;

interface GenerationRepositoryInterface
{
	/**
	 * Get a generation by its id.
	 *
	 * @throws GenerationNotFoundException if no generation exists with this id.
	 */
	public function getById(GenerationId $generationId) : Generation;

	/**
	 * Get a generation by its identifier
	 *
	 * @throws GenerationNotFoundException if no generation exists with this
	 *     identifier.
	 */
	public function getByIdentifier(string $identifier) : Generation;

	/**
	 * Get generations that this Pokémon has appeared in (via version groups).
	 *
	 * @return Generation[] Indexed by id. Ordered by id.
	 */
	public function getWithPokemon(PokemonId $pokemonId) : array;

	/**
	 * Get generations that this move has appeared in (via version groups).
	 *
	 * @return Generation[] Indexed by id. Ordered by id.
	 */
	public function getWithMove(MoveId $moveId) : array;

	/**
	 * Get generations since the given generation, inclusive.
	 *
	 * @return Generation[] Indexed by id. Ordered by id.
	 */
	public function getSince(GenerationId $generationId) : array;
}
