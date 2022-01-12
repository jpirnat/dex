<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Usage\Averaged;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;

interface UsageRatedAveragedPokemonRepositoryInterface
{
	/**
	 * Get usage rated averaged Pokémon records by their start month, end month,
	 * format, and rating.
	 *
	 * @return UsageRatedAveragedPokemon[] Indexed by Pokémon id.
	 */
	public function getByMonthsAndFormatAndRating(
		DateTime $start,
		DateTime $end,
		FormatId $formatId,
		int $rating
	) : array;
}
