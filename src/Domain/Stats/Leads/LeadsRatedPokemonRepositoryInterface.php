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
	public function has(
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
	 * Get leads rated Pokémon records by format and Pokémon.
	 *
	 * @param FormatId $formatId
	 * @param PokemonId $pokemonId
	 *
	 * @return LeadsRatedPokemon[]
	 */
	public function getByFormatAndPokemon(
		FormatId $formatId,
		PokemonId $pokemonId
	) : array;
}
