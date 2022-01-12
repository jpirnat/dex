<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Usage;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;

interface UsageRatedRepositoryInterface
{
	/**
	 * Does a usage rated record exist for this month, format, and rating?
	 */
	public function has(DateTime $month, FormatId $formatId, int $rating) : bool;

	/**
	 * Save a usage rated record.
	 */
	public function save(UsageRated $usageRated) : void;
}
