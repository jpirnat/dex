<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Leads\Averaged;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;

interface LeadsAveragedPokemonRepositoryInterface
{
	/**
	 * Get leads averaged Pokémon records by their start month, end month, and
	 * format. Indexed by Pokémon id value.
	 *
	 * @param DateTime $start
	 * @param DateTime $end
	 * @param FormatId $formatId
	 *
	 * @return LeadsAveragedPokemon[]
	 */
	public function getByMonthsAndFormat(
		DateTime $start,
		DateTime $end,
		FormatId $formatId
	) : array;
}
