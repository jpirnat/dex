<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Usage;

use Jp\Dex\Domain\Formats\FormatId;

interface UsageRatedRepositoryInterface
{
	/**
	 * Does a usage rated record exist for this year, month, format, and rating?
	 *
	 * @param int $year
	 * @param int $month
	 * @param FormatId $formatId
	 * @param int $rating
	 *
	 * @return bool
	 */
	public function has(
		int $year,
		int $month,
		FormatId $formatId,
		int $rating
	) : bool;

	/**
	 * Save a usage rated record.
	 *
	 * @param UsageRated $usageRated
	 *
	 * @return void
	 */
	public function save(UsageRated $usageRated) : void;
}
