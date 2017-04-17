<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Usage;

use Jp\Dex\Domain\Formats\FormatId;

class UsageRated
{
	/** @var int $year */
	protected $year;

	/** @var int $month */
	protected $month;

	/** @var FormatId $formatId */
	protected $formatId;

	/** @var int $rating */
	protected $rating;

	/** @var float $averageWeightPerTeam */
	protected $averageWeightPerTeam;

	/**
	 * Constructor.
	 *
	 * @param int $year
	 * @param int $month
	 * @param FormatId $formatId
	 * @param int $rating
	 * @param float $averageWeightPerTeam
	 */
	public function __construct(
		int $year,
		int $month,
		FormatId $formatId,
		int $rating,
		float $averageWeightPerTeam
	) {
		// TODO: validation

		$this->year = $year;
		$this->month = $month;
		$this->formatId = $formatId;
		$this->rating = $rating;
		$this->averageWeightPerTeam = $averageWeightPerTeam;
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
	 * Get the rating.
	 *
	 * @return int
	 */
	public function rating() : int
	{
		return $this->rating;
	}

	/**
	 * Get the average weight per team.
	 *
	 * @return float
	 */
	public function averageWeightPerTeam() : float
	{
		return $this->averageWeightPerTeam;
	}
}
