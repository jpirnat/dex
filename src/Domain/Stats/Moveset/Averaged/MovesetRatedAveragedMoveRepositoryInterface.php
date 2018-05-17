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
	 * format, rating, and Pokémon. Indexed by move id value.
	 *
	 * @param DateTime $start
	 * @param DateTime $end
	 * @param FormatId $formatId
	 * @param int $rating
	 * @param PokemonId $pokemonId
	 *
	 * @return MovesetRatedAveragedMove[]
	 */
	public function getByMonthsAndFormatAndRatingAndPokemon(
		DateTime $start,
		DateTime $end,
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId
	) : array;
}
