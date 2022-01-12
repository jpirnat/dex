<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Items;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Versions\GenerationId;

interface StatsPokemonItemRepositoryInterface
{
	/**
	 * Get stats Pokémon items by month, format, rating, and Pokémon.
	 *
	 * @return StatsPokemonItem[] Ordered by percent descending.
	 */
	public function getByMonth(
		DateTime $month,
		?DateTime $prevMonth,
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId,
		GenerationId $generationId,
		LanguageId $languageId
	) : array;
}
