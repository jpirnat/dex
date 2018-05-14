<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Leads;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;

interface LeadsPokemonRepositoryInterface
{
	/**
	 * Do any leads Pokémon records exist for this month and format?
	 *
	 * @param DateTime $month
	 * @param FormatId $formatId
	 *
	 * @return bool
	 */
	public function hasAny(DateTime $month, FormatId $formatId) : bool;

	/**
	 * Save a leads Pokémon record.
	 *
	 * @param LeadsPokemon $leadsPokemon
	 *
	 * @return void
	 */
	public function save(LeadsPokemon $leadsPokemon) : void;

	/**
	 * Get leads Pokémon records by month and format. Indexed by Pokémon id
	 * value. Use this to recreate a stats leads file, such as
	 * http://www.smogon.com/stats/leads/2014-11/ou-1695.txt.
	 *
	 * @param DateTime $month
	 * @param FormatId $formatId
	 *
	 * @return LeadsPokemon[]
	 */
	public function getByMonthAndFormat(DateTime $month, FormatId $formatId) : array;
}
