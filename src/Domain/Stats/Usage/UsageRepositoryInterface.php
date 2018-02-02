<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Usage;

use Jp\Dex\Domain\Formats\FormatId;

interface UsageRepositoryInterface
{
	/**
	 * Does a usage record exist for this year, month, and format?
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
	 * Do any usage records exist for this year and month?
	 *
	 * @param int $year
	 * @param int $month
	 *
	 * @return bool
	 */
	public function hasAny(
		int $year,
		int $month
	) : bool;

	/**
	 * Save a usage record.
	 *
	 * @param Usage $usage
	 *
	 * @return void
	 */
	public function save(Usage $usage) : void;
}
