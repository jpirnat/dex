<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Moves;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Versions\VersionGroupId;

interface StatsPokemonMoveRepositoryInterface
{
	/**
	 * Get stats Pokémon moves by month, format, rating, and Pokémon.
	 *
	 * @return StatsPokemonMove[] Ordered by percent descending.
	 */
	public function getByMonth(
		DateTime $month,
		?DateTime $prevMonth,
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId,
		VersionGroupId $versionGroupId,
		LanguageId $languageId,
	) : array;
}
