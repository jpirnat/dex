<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Leads;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Languages\LanguageId;

interface StatsLeadsPokemonRepositoryInterface
{
	/**
	 * Get stats leads Pokémon by month, format, and rating.
	 *
	 * @return StatsLeadsPokemon[] Ordered by rank ascending.
	 */
	public function getByMonth(
		DateTime $month,
		?DateTime $prevMonth,
		FormatId $formatId,
		int $rating,
		LanguageId $languageId,
	) : array;
}
