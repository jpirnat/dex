<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Usage;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Items\ItemId;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Versions\GenerationId;

interface StatsItemPokemonRepositoryInterface
{
	/**
	 * Get stats item Pokémon by month, format, rating, and item.
	 *
	 * @param DateTime $month
	 * @param DateTime|null $prevMonth
	 * @param FormatId $formatId
	 * @param int $rating
	 * @param ItemId $itemId
	 * @param GenerationId $generationId
	 * @param LanguageId $languageId
	 *
	 * @return StatsItemPokemon[] Ordered by usage percent descending.
	 */
	public function getByMonth(
		DateTime $month,
		?DateTime $prevMonth,
		FormatId $formatId,
		int $rating,
		ItemId $itemId,
		GenerationId $generationId,
		LanguageId $languageId
	) : array;
}
