<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Moveset\Averaged;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Pokemon\PokemonId;

interface MovesetRatedAveragedMoveRepositoryInterface
{
	/**
	 * Get moveset rated averaged move records by their start month, end month,
	 * format, rating, and Pokémon.
	 *
	 * @return MovesetRatedAveragedMove[] Indexed by move id.
	 */
	public function getByMonthsAndFormatAndRatingAndPokemon(
		DateTime $start,
		DateTime $end,
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId,
	) : array;
}
