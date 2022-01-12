<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Moveset\Averaged;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Pokemon\PokemonId;

interface MovesetRatedAveragedItemRepositoryInterface
{
	/**
	 * Get moveset rated averaged item records by their start month, end month,
	 * format, rating, and Pokémon.
	 *
	 * @return MovesetRatedAveragedItem[] Indexed by item id.
	 */
	public function getByMonthsAndFormatAndRatingAndPokemon(
		DateTime $start,
		DateTime $end,
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId
	) : array;
}
