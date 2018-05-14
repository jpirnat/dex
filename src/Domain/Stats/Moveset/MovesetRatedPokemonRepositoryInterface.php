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
	 *
	 * @param DateTime $month
	 * @param FormatId $formatId
	 * @param int $rating
	 * @param PokemonId $pokemonId
	 *
	 * @return bool
	 */
	public function has(
		DateTime $month,
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId
	) : bool;

	/**
	 * Do any moveset rated Pokémon records exist for this month, format, and
	 * rating?
	 *
	 * @param DateTime $month
	 * @param FormatId $formatId
	 * @param int $rating
	 *
	 * @return bool
	 */
	public function hasAny(DateTime $month, FormatId $formatId, int $rating) : bool;

	/**
	 * Save a moveset rated Pokémon record.
	 *
	 * @param MovesetRatedPokemon $movesetRatedPokemon
	 *
	 * @return void
	 */
	public function save(MovesetRatedPokemon $movesetRatedPokemon) : void;

	/**
	 * Get a moveset rated Pokémon record by month, format, rating, and Pokémon.
	 *
	 * @param DateTime $month
	 * @param FormatId $formatId
	 * @param int $rating
	 * @param PokemonId $pokemonId
	 *
	 * @throws MovesetRatedPokemonNotFoundException if no moveset rated Pokémon
	 *     record exists with this month, format, rating, and Pokémon.
	 *
	 * @return MovesetRatedPokemon
	 */
	public function getByMonthAndFormatAndRatingAndPokemon(
		DateTime $month,
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId
	) : MovesetRatedPokemon;
}
