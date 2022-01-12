<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Usage\Averaged;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;

interface UsageAveragedPokemonRepositoryInterface
{
	/**
	 * Get usage averaged Pokémon records by their start month, end month, and
	 * format.
	 *
	 * @return UsageAveragedPokemon[] Indexed by Pokémon id.
	 */
	public function getByMonthsAndFormat(
		DateTime $start,
		DateTime $end,
		FormatId $formatId
	) : array;
}
