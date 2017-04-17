<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Leads;

use Jp\Dex\Domain\Formats\FormatId;

class Leads
{
	/** @var int $year */
	protected $year;

	/** @var int $month */
	protected $month;

	/** @var FormatId $formatId */
	protected $formatId;

	/** @var int $totalLeads */
	protected $totalLeads;

	/**
	 * Constructor.
	 *
	 * @param int $year
	 * @param int $month
	 * @param FormatId $formatId
	 * @param int $totalLeads
	 */
	public function __construct(
		int $year,
		int $month,
		FormatId $formatId,
		int $totalLeads
	) {
		// TODO: validation

		$this->year = $year;
		$this->month = $month;
		$this->formatId = $formatId;
		$this->totalLeads = $totalLeads;
	}

	/**
	 * Get the year.
	 *
	 * @return int
	 */
	public function year() : int
	{
		return $this->year;
	}

	/**
	 * Get the month.
	 *
	 * @return int
	 */
	public function month() : int
	{
		return $this->month;
	}

	/**
	 * Get the format id.
	 *
	 * @return FormatId
	 */
	public function formatId() : FormatId
	{
		return $this->formatId;
	}

	/**
	 * Get the total leads.
	 *
	 * @return int
	 */
	public function totalLeads() : int
	{
		return $this->totalLeads;
	}
}
