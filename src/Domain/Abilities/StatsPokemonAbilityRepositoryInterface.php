<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Abilities;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\PokemonId;

interface StatsPokemonAbilityRepositoryInterface
{
	/**
	 * Get stats Pokémon abilities by month, format, rating, and Pokémon.
	 *
	 * @return StatsPokemonAbility[] Ordered by percent descending.
	 */
	public function getByMonth(
		DateTime $month,
		?DateTime $prevMonth,
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId,
		LanguageId $languageId,
	) : array;
}
