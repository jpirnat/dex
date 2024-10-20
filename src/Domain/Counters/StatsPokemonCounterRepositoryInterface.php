<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Counters;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\PokemonId;

interface StatsPokemonCounterRepositoryInterface
{
	/**
	 * Get stats Pokémon counters by month, format, rating, and Pokémon.
	 *
	 * @return StatsPokemonCounter[] Ordered by score descending.
	 */
	public function getByMonth(
		DateTime $month,
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId,
		LanguageId $languageId,
	) : array;
}
