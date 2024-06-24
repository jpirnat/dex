<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Leads;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Stats\Exceptions\InvalidCountException;
use Jp\Dex\Domain\Stats\Exceptions\InvalidMonthException;
use Jp\Dex\Domain\Stats\ValidateMonthTrait;

final readonly class Leads
{
	use ValidateMonthTrait;

	/**
	 * Constructor.
	 *
	 * @throws InvalidMonthException if $month is invalid.
	 * @throws InvalidCountException if $totalLeads is invalid.
	 */
	public function __construct(
		private DateTime $month,
		private FormatId $formatId,
		private int $totalLeads,
	) {
		$this->validateMonth($month);

		if ($totalLeads < 0) {
			throw new InvalidCountException("Invalid number of total leads: $totalLeads.");
		}
	}

	/**
	 * Get the month.
	 */
	public function getMonth() : DateTime
	{
		return $this->month;
	}

	/**
	 * Get the format id.
	 */
	public function getFormatId() : FormatId
	{
		return $this->formatId;
	}

	/**
	 * Get the total leads.
	 */
	public function getTotalLeads() : int
	{
		return $this->totalLeads;
	}
}
