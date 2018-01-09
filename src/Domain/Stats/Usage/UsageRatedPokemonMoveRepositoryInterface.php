<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Usage;

use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Moves\MoveId;

interface UsageRatedPokemonMoveRepositoryInterface
{
	/**
	 * Get usage rated Pokémon move records by their year, month, format,
	 * rating, and move. Indexed by Pokémon id value.
	 *
	 * @param int $year
	 * @param int $month
	 * @param FormatId $formatId
	 * @param int $rating
	 * @param MoveId $moveId
	 *
	 * @return UsageRatedPokemonMove[]
	 */
	public function getByYearAndMonthAndFormatAndRatingAndMove(
		int $year,
		int $month,
		FormatId $formatId,
		int $rating,
		MoveId $moveId
	) : array;
}
