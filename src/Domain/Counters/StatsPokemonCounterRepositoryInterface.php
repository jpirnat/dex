<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Counters;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Versions\VersionGroupId;

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
		VersionGroupId $versionGroupId,
		LanguageId $languageId,
	) : array;
}
