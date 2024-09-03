<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Types;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\PokemonId;

interface StatsPokemonTeraTypeRepositoryInterface
{
	/**
	 * Get stats Pokémon Tera types by month, format, rating, and Pokémon.
	 *
	 * @return StatsPokemonTeraType[] Ordered by percent descending.
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
