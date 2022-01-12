<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Leads;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Versions\GenerationId;

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
		GenerationId $generationId,
		LanguageId $languageId
	) : array;
}
