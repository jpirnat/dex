<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Moveset;

use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Pokemon\PokemonId;

interface MovesetPokemonRepositoryInterface
{
	/**
	 * Do any moveset Pokémon records exist for this year, month, and format?
	 *
	 * @param int $year
	 * @param int $month
	 * @param FormatId $formatId
	 *
	 * @return bool
	 */
	public function has(
		int $year,
		int $month,
		FormatId $formatId
	) : bool;

	/**
	 * Save a moveset Pokémon record.
	 *
	 * @param MovesetPokemon $movesetPokemon
	 *
	 * @return void
	 */
	public function save(MovesetPokemon $movesetPokemon) : void;

	/**
	 * Get a moveset Pokémon record by year, month, format, and Pokémon.
	 *
	 * @param int $year
	 * @param int $month
	 * @param FormatId $formatId
	 * @param PokemonId $pokemonId
	 *
	 * @throws MovesetPokemonNotFoundException if no moveset Pokémon record
	 *     exists with this year, month, format, and Pokémon.
	 *
	 * @return MovesetPokemon
	 */
	public function getByYearAndMonthAndFormatAndPokemon(
		int $year,
		int $month,
		FormatId $formatId,
		PokemonId $pokemonId
	) : MovesetPokemon;
}
