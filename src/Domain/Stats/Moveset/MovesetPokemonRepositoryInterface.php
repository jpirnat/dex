<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Moveset;

use Jp\Dex\Domain\Formats\FormatId;

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
}
