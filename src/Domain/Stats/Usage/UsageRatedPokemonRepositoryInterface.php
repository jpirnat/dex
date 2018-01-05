<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Usage;

use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Pokemon\PokemonId;

interface UsageRatedPokemonRepositoryInterface
{
	/**
	 * Do any usage rated Pokémon records exist for this year, month, format,
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
	 * Save a usage rated Pokémon record.
	 *
	 * @param UsageRatedPokemon $usageRatedPokemon
	 *
	 * @return void
	 */
	public function save(UsageRatedPokemon $usageRatedPokemon) : void;

	/**
	 * Get usage rated Pokémon records by year and month and format and rating.
	 * Indexed by Pokémon id value. Use this to recreate a stats usage file,
	 * such as http://www.smogon.com/stats/2014-11/ou-1695.txt.
	 *
	 * @param int $year
	 * @param int $month
	 * @param FormatId $formatId
	 * @param int $rating
	 *
	 * @return UsageRatedPokemon[]
	 */
	public function getByYearAndMonthAndFormatAndRating(
		int $year,
		int $month,
		FormatId $formatId,
		int $rating
	) : array;

	/**
	 * Get usage rated Pokémon records by format and Pokémon.
	 *
	 * @param FormatId $formatId
	 * @param PokemonId $pokemonId
	 *
	 * @return UsageRatedPokemon[]
	 */
	public function getByFormatAndPokemon(
		FormatId $formatId,
		PokemonId $pokemonId
	) : array;
}
