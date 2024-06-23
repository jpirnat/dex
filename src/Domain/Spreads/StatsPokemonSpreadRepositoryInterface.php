<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Spreads;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\PokemonId;

interface StatsPokemonSpreadRepositoryInterface
{
	/**
	 * Get stats Pokémon spreads by month, format, rating, and Pokémon.
	 *
	 * @return StatsPokemonSpread[] Ordered by percent descending.
	 */
	public function getByMonth(
		DateTime $month,
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId,
		LanguageId $languageId,
	) : array;
}
