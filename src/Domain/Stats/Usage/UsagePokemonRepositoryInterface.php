<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Usage;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;

interface UsagePokemonRepositoryInterface
{
	/**
	 * Do any usage Pokémon records exist for this month and format?
	 *
	 * @param DateTime $month
	 * @param FormatId $formatId
	 *
	 * @return bool
	 */
	public function hasAny(DateTime $month, FormatId $formatId) : bool;

	/**
	 * Save a usage Pokémon record.
	 *
	 * @param UsagePokemon $usagePokemon
	 *
	 * @return void
	 */
	public function save(UsagePokemon $usagePokemon) : void;
}
