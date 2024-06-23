<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Usage;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Versions\VersionGroupId;

interface StatsMovePokemonRepositoryInterface
{
	/**
	 * Get stats move Pokémon by month, format, rating, and move.
	 *
	 * @return StatsMovePokemon[] Ordered by usage percent descending.
	 */
	public function getByMonth(
		DateTime $month,
		?DateTime $prevMonth,
		FormatId $formatId,
		int $rating,
		MoveId $moveId,
		VersionGroupId $versionGroupId,
		LanguageId $languageId,
	) : array;
}
