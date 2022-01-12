<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Leads;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;

interface LeadsPokemonRepositoryInterface
{
	/**
	 * Do any leads Pokémon records exist for this month and format?
	 */
	public function hasAny(DateTime $month, FormatId $formatId) : bool;

	/**
	 * Save a leads Pokémon record.
	 */
	public function save(LeadsPokemon $leadsPokemon) : void;
}
