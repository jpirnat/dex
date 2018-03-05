<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Usage\Derived;

use Jp\Dex\Domain\Abilities\AbilityId;
use Jp\Dex\Domain\Formats\FormatId;

interface UsageRatedPokemonAbilityRepositoryInterface
{
	/**
	 * Get usage rated Pokémon ability records by their year, month, format,
	 * rating, and ability. Indexed by Pokémon id value.
	 *
	 * @param int $year
	 * @param int $month
	 * @param FormatId $formatId
	 * @param int $rating
	 * @param AbilityId $abilityId
	 *
	 * @return UsageRatedPokemonAbility[]
	 */
	public function getByYearAndMonthAndFormatAndRatingAndAbility(
		int $year,
		int $month,
		FormatId $formatId,
		int $rating,
		AbilityId $abilityId
	) : array;
}
