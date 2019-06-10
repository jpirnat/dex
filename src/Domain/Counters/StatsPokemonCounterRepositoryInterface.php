<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Counters;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Versions\GenerationId;

interface StatsPokemonCounterRepositoryInterface
{
	/**
	 * Get stats Pokémon counters by month, format, rating, and Pokémon.
	 *
	 * @param DateTime $month
	 * @param FormatId $formatId
	 * @param int $rating
	 * @param PokemonId $pokemonId
	 * @param GenerationId $generationId
	 * @param LanguageId $languageId
	 *
	 * @return StatsPokemonCounter[] Ordered by score descending.
	 */
	public function getByMonth(
		DateTime $month,
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId,
		GenerationId $generationId,
		LanguageId $languageId
	) : array;
}
