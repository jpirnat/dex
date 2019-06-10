<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Leads;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Pokemon\PokemonId;

interface LeadsRatedPokemonRepositoryInterface
{
	/**
	 * Do any leads rated Pokémon records exist for this month, format, and
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
	 * Save a leads rated Pokémon record.
	 *
	 * @param LeadsRatedPokemon $leadsRatedPokemon
	 *
	 * @return void
	 */
	public function save(LeadsRatedPokemon $leadsRatedPokemon) : void;

	/**
	 * Get leads rated Pokémon records by their format, rating, and Pokémon.
	 * Use this to create a trend line for a Pokémon's lead usage in a format.
	 * Indexed and sorted by month.
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
