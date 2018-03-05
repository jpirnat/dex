<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Usage\Derived;

use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Items\ItemId;

interface UsageRatedPokemonItemRepositoryInterface
{
	/**
	 * Get usage rated Pokémon item records by their year, month, format,
	 * rating, and item. Indexed by Pokémon id value.
	 *
	 * @param int $year
	 * @param int $month
	 * @param FormatId $formatId
	 * @param int $rating
	 * @param ItemId $itemId
	 *
	 * @return UsageRatedPokemonItem[]
	 */
	public function getByYearAndMonthAndFormatAndRatingAndItem(
		int $year,
		int $month,
		FormatId $formatId,
		int $rating,
		ItemId $itemId
	) : array;
}
