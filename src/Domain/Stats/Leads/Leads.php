<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Leads;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Stats\Exceptions\InvalidCountException;
use Jp\Dex\Domain\Stats\Exceptions\InvalidMonthException;
use Jp\Dex\Domain\Stats\ValidateMonthTrait;

final class Leads
{
	use ValidateMonthTrait;

	/** @var DateTime $month */
	private $month;

	/** @var FormatId $formatId */
	private $formatId;

	/** @var int $totalLeads */
	private $totalLeads;

	/**
	 * Constructor.
	 *
	 * @param DateTime $month
	 * @param FormatId $formatId
	 * @param int $totalLeads
	 *
	 * @throws InvalidMonthException if $month is invalid.
	 * @throws InvalidCountException if $totalLeads is invalid.
	 */
	public function __construct(
		DateTime $month,
		FormatId $formatId,
		int $totalLeads
	) {
		$this->validateMonth($month);

		if ($totalLeads < 0) {
			throw new InvalidCountException(
				'Invalid number of total leads: ' . $totalLeads
			);
		}

		$this->month = $month;
		$this->formatId = $formatId;
		$this->totalLeads = $totalLeads;
	}

	/**
	 * Get the month.
	 *
	 * @return DateTime
	 */
	public function getMonth() : DateTime
	{
		return $this->month;
	}

	/**
	 * Get the format id.
	 *
	 * @return FormatId
	 */
	public function getFormatId() : FormatId
	{
		return $this->formatId;
	}

	/**
	 * Get the total leads.
	 *
	 * @return int
	 */
	public function getTotalLeads() : int
	{
		return $this->totalLeads;
	}
}
