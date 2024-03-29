<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Leads;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;

interface LeadsRatedPokemonRepositoryInterface
{
	/**
	 * Do any leads rated Pokémon records exist for this month, format, and rating?
	 */
	public function hasAny(DateTime $month, FormatId $formatId, int $rating) : bool;

	/**
	 * Save a leads rated Pokémon record.
	 */
	public function save(LeadsRatedPokemon $leadsRatedPokemon) : void;
}
