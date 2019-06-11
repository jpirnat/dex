<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Usage;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Versions\GenerationId;

interface StatsMovePokemonRepositoryInterface
{
	/**
	 * Get stats move Pokémon by month, format, rating, and move.
	 *
	 * @param DateTime $month
	 * @param DateTime|null $prevMonth
	 * @param FormatId $formatId
	 * @param int $rating
	 * @param MoveId $moveId
	 * @param GenerationId $generationId
	 * @param LanguageId $languageId
	 *
	 * @return StatsMovePokemon[] Ordered by usage percent descending.
	 */
	public function getByMonth(
		DateTime $month,
		?DateTime $prevMonth,
		FormatId $formatId,
		int $rating,
		MoveId $moveId,
		GenerationId $generationId,
		LanguageId $languageId
	) : array;
}
