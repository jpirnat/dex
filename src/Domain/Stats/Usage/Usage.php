<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Usage;

use Jp\Dex\Domain\Formats\FormatId;

class Usage
{
	/** @var int $year */
	protected $year;

	/** @var int $month */
	protected $month;

	/** @var FormatId $formatId */
	protected $formatId;

	/** @var int $totalBattles */
	protected $totalBattles;

	/**
	 * Constructor.
	 *
	 * @param int $year
	 * @param int $month
	 * @param FormatId $formatId
	 * @param int $totalBattles
	 */
	public function __construct(
		int $year,
		int $month,
		FormatId $formatId,
		int $totalBattles
	) {
		// TODO: validation

		$this->year = $year;
		$this->month = $month;
		$this->formatId = $formatId;
		$this->totalBattles = $totalBattles;
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
	 * Get the total battles.
	 *
	 * @return int
	 */
	public function totalBattles() : int
	{
		return $this->totalBattles;
	}
}
