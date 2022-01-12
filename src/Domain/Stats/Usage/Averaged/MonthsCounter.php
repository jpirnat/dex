<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Usage\Averaged;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedPokemonRepositoryInterface;

final class MonthsCounter
{
	public function __construct(
		private MovesetRatedPokemonRepositoryInterface $movesetRatedPokemonRepository,
	) {}

	/**
	 * Count the number of months from start to end.
	 */
	public function countAllMonths(DateTime $start, DateTime $end) : int
	{
		$startYear = (int) $start->format('Y');
		$startMonth = (int) $start->format('m');
		$endYear = (int) $end->format('Y');
		$endMonth = (int) $end->format('m');
		return ($endYear - $startYear) * 12 + ($endMonth - $startMonth) + 1;
	}

	/**
	 * Count the months of moveset data for this Pokémon.
	 */
	public function countMovesetMonths(
		DateTime $start,
		DateTime $end,
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId
	) : int {
		return $this->movesetRatedPokemonRepository->count(
			$start,
			$end,
			$formatId,
			$rating,
			$pokemonId
		);
	}

	/**
	 * Count the months of moveset data for all Pokémon.
	 *
	 * @return int[] Indexed by Pokémon id.
	 */
	public function countMovesetMonthsAll(
		DateTime $start,
		DateTime $end,
		FormatId $formatId,
		int $rating
	) : array {
		return $this->movesetRatedPokemonRepository->countAll(
			$start,
			$end,
			$formatId,
			$rating
		);
	}
}
