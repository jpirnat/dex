<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Moveset;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Pokemon\PokemonId;

interface MovesetRatedPokemonRepositoryInterface
{
	/**
	 * Does a moveset rated Pokémon record exist for this month, format, rating,
	 * and Pokémon?
	 */
	public function has(
		DateTime $month,
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId,
	) : bool;

	/**
	 * Do any moveset rated Pokémon records exist for this month, format, and
	 * rating?
	 */
	public function hasAny(DateTime $month, FormatId $formatId, int $rating) : bool;

	/**
	 * Count the moveset rated Pokémon records for this start month, end month,
	 * format, rating, and Pokémon.
	 */
	public function count(
		DateTime $start,
		DateTime $end,
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId,
	) : int;

	/**
	 * Count the moveset rated Pokémon records for this start month, end month,
	 * format, and rating.
	 *
	 * @return int[] Indexed by Pokémon id.
	 */
	public function countAll(
		DateTime $start,
		DateTime $end,
		FormatId $formatId,
		int $rating,
	) : array;

	/**
	 * Save a moveset rated Pokémon record.
	 */
	public function save(MovesetRatedPokemon $movesetRatedPokemon) : void;

	/**
	 * Get a moveset rated Pokémon record by month, format, rating, and Pokémon.
	 */
	public function getByMonthAndFormatAndRatingAndPokemon(
		DateTime $month,
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId,
	) : ?MovesetRatedPokemon;
}
