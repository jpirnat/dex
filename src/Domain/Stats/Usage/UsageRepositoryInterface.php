<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Usage;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;

interface UsageRepositoryInterface
{
	/**
	 * Does a usage record exist for this month and format?
	 */
	public function has(DateTime $month, FormatId $formatId) : bool;

	/**
	 * Save a usage record.
	 */
	public function save(Usage $usage) : void;
}
