<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Leads\Averaged;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;

interface LeadsAveragedPokemonRepositoryInterface
{
	/**
	 * Get leads averaged Pokémon records by their start month, end month, and
	 * format.
	 *
	 * @return LeadsAveragedPokemon[] Indexed by Pokémon id.
	 */
	public function getByMonthsAndFormat(
		DateTime $start,
		DateTime $end,
		FormatId $formatId
	) : array;
}
