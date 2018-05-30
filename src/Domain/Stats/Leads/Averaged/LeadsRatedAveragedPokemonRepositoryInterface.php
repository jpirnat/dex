<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Leads\Averaged;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;

interface LeadsRatedAveragedPokemonRepositoryInterface
{
	/**
	 * Get leads rated averaged Pokémon records by their start month, end month,
	 * format, and rating. Indexed by Pokémon id value.
	 *
	 * @param DateTime $start
	 * @param DateTime $end
	 * @param FormatId $formatId
	 * @param int $rating
	 *
	 * @return LeadsRatedAveragedPokemon[]
	 */
	public function getByMonthsAndFormatAndRating(
		DateTime $start,
		DateTime $end,
		FormatId $formatId,
		int $rating
	) : array;
}
