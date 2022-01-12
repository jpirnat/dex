<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Leads\Averaged;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;

interface LeadsRatedAveragedPokemonRepositoryInterface
{
	/**
	 * Do any leads rated averaged Pokémon records exist for this start month,
	 * end month, format, and rating?
	 */
	public function hasAny(
		DateTime $start,
		DateTime $end,
		FormatId $formatId,
		int $rating
	) : bool;

	/**
	 * Get leads rated averaged Pokémon records by their start month, end month,
	 * format, and rating.
	 *
	 * @return LeadsRatedAveragedPokemon[] Indexed by Pokémon id.
	 */
	public function getByMonthsAndFormatAndRating(
		DateTime $start,
		DateTime $end,
		FormatId $formatId,
		int $rating
	) : array;
}
