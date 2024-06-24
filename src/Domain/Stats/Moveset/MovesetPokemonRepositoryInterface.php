<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Moveset;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Pokemon\PokemonId;

interface MovesetPokemonRepositoryInterface
{
	/**
	 * Do any moveset Pokémon records exist for this month and format?
	 */
	public function hasAny(DateTime $month, FormatId $formatId) : bool;

	/**
	 * Save a moveset Pokémon record.
	 */
	public function save(MovesetPokemon $movesetPokemon) : void;

	/**
	 * Get a moveset Pokémon record by month, format, and Pokémon.
	 */
	public function getByMonthAndFormatAndPokemon(
		DateTime $month,
		FormatId $formatId,
		PokemonId $pokemonId,
	) : ?MovesetPokemon;
}
