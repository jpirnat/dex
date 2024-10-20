<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Usage;

use DateTime;
use Jp\Dex\Domain\Abilities\AbilityId;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Languages\LanguageId;

interface StatsAbilityPokemonRepositoryInterface
{
	/**
	 * Get stats ability Pokémon by month, format, rating, and ability.
	 *
	 * @return StatsAbilityPokemon[] Ordered by usage percent descending.
	 */
	public function getByMonth(
		DateTime $month,
		?DateTime $prevMonth,
		FormatId $formatId,
		int $rating,
		AbilityId $abilityId,
		LanguageId $languageId,
	) : array;
}
