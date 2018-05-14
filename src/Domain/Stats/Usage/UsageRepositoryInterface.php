<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Usage;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;

interface UsageRepositoryInterface
{
	/**
	 * Does a usage record exist for this month and format?
	 *
	 * @param DateTime $month
	 * @param FormatId $formatId
	 *
	 * @return bool
	 */
	public function has(DateTime $month, FormatId $formatId) : bool;

	/**
	 * Do any usage records exist for this month?
	 *
	 * @param DateTime $month
	 *
	 * @return bool
	 */
	public function hasAny(DateTime $month) : bool;

	/**
	 * Save a usage record.
	 *
	 * @param Usage $usage
	 *
	 * @return void
	 */
	public function save(Usage $usage) : void;
}
