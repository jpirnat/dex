<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats;

use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Pokemon\PokemonId;

interface MovesetRatedMoveRepositoryInterface
{
	/**
	 * Save a moveset rated move record.
	 *
	 * @param MovesetRatedMove $movesetRatedMove
	 *
	 * @return void
	 */
	public function save(MovesetRatedMove $movesetRatedMove) : void;

	/**
	 * Get moveset rated move records by format and rating and Pokémon.
	 *
	 * @param FormatId $formatId
	 * @param int $rating
	 * @param PokemonId $pokemonId
	 *
	 * @return MovesetRatedMove[]
	 */
	public function getByFormatAndRatingAndPokemon(
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId
	) : array;

	/**
	 * Get moveset rated move records by format and Pokémon and move.
	 *
	 * @param FormatId $formatId
	 * @param PokemonId $pokemonId
	 * @param MoveId $moveId
	 *
	 * @return MovesetRatedMove[]
	 */
	public function getByFormatAndPokemonAndMove(
		FormatId $formatId,
		PokemonId $pokemonId,
		MoveId $moveId
	) : array;
}
