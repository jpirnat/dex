<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Usage;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Pokemon\PokemonId;

interface UsageRatedPokemonRepositoryInterface
{
	/**
	 * Do any usage rated Pokémon records exist for this month, format, and
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
	 * Save a usage rated Pokémon record.
	 *
	 * @param UsageRatedPokemon $usageRatedPokemon
	 *
	 * @return void
	 */
	public function save(UsageRatedPokemon $usageRatedPokemon) : void;

	/**
	 * Get usage rated Pokémon records by month, format, and rating. Indexed by
	 * Pokémon id value. Use this to recreate a stats usage file, such as
	 * http://www.smogon.com/stats/2014-11/ou-1695.txt.
	 *
	 * @param DateTime $month
	 * @param FormatId $formatId
	 * @param int $rating
	 *
	 * @return UsageRatedPokemon[]
	 */
	public function getByMonthAndFormatAndRating(
		DateTime $month,
		FormatId $formatId,
		int $rating
	) : array;

	/**
	 * Get usage rated Pokémon records by their format, rating, and Pokémon.
	 * Use this to create a trend line for a Pokémon's usage in a format.
	 * Indexed and sorted by month.
	 *
	 * @param FormatId $formatId
	 * @param int $rating
	 * @param PokemonId $pokemonId
	 *
	 * @return UsageRatedPokemon[]
	 */
	public function getByFormatAndRatingAndPokemon(
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId
	) : array;
}
