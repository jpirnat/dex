<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Leads;

use Jp\Dex\Domain\Formats\FormatId;

interface LeadsPokemonRepositoryInterface
{
	/**
	 * Do any leads Pokémon records exist for this year, month, and format?
	 *
	 * @param int $year
	 * @param int $month
	 * @param FormatId $formatId
	 *
	 * @return bool
	 */
	public function has(
		int $year,
		int $month,
		FormatId $formatId
	) : bool;

	/**
	 * Save a leads Pokémon record.
	 *
	 * @param LeadsPokemon $leadsPokemon
	 *
	 * @return void
	 */
	public function save(LeadsPokemon $leadsPokemon) : void;
}
