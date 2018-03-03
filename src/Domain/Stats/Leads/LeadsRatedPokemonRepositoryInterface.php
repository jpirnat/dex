<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Leads;

use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Pokemon\PokemonId;

interface LeadsRatedPokemonRepositoryInterface
{
	/**
	 * Do any leads rated Pokémon records exist for this year, month, format,
	 * and rating?
	 *
	 * @param int $year
	 * @param int $month
	 * @param FormatId $formatId
	 * @param int $rating
	 *
	 * @return bool
	 */
	public function hasAny(
		int $year,
		int $month,
		FormatId $formatId,
		int $rating
	) : bool;

	/**
	 * Save a leads rated Pokémon record.
	 *
	 * @param LeadsRatedPokemon $leadsRatedPokemon
	 *
	 * @return void
	 */
	public function save(LeadsRatedPokemon $leadsRatedPokemon) : void;

	/**
	 * Get leads rated Pokémon records by year and month and format and rating.
	 * Indexed by Pokémon id value. Use this to recreate a stats leads file,
	 * such as http://www.smogon.com/stats/leads/2014-11/ou-1695.txt.
	 *
	 * @param int $year
	 * @param int $month
	 * @param FormatId $formatId
	 * @param int $rating
	 *
	 * @return LeadsRatedPokemon[]
	 */
	public function getByYearAndMonthAndFormatAndRating(
		int $year,
		int $month,
		FormatId $formatId,
		int $rating
	) : array;

	/**
	 * Get leads rated Pokémon records by their format, rating, and Pokémon.
	 * Use this to create a trend line for a Pokémon's lead usage in a format.
	 *
	 * @param FormatId $formatId
	 * @param int $rating
	 * @param PokemonId $pokemonId
	 *
	 * @return LeadsRatedPokemon[]
	 */
	public function getByFormatAndRatingAndPokemon(
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId
	) : array;
}
