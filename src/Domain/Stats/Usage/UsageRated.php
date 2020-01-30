<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Usage;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Stats\Exceptions\InvalidAverageWeightPerTeamException;
use Jp\Dex\Domain\Stats\Exceptions\InvalidMonthException;
use Jp\Dex\Domain\Stats\Exceptions\InvalidRatingException;
use Jp\Dex\Domain\Stats\ValidateMonthTrait;

final class UsageRated
{
	use ValidateMonthTrait;

	private DateTime $month;
	private FormatId $formatId;
	private int $rating;
	private float $averageWeightPerTeam;

	/**
	 * Constructor.
	 *
	 * @param DateTime $month
	 * @param FormatId $formatId
	 * @param int $rating
	 * @param float $averageWeightPerTeam
	 *
	 * @throws InvalidMonthException if $month is invalid.
	 * @throws InvalidRatingException if $rating is invalid.
	 * @throws InvalidAverageWeightPerTeamException if $averageWeightPerTeam is
	 *     invalid.
	 */
	public function __construct(
		DateTime $month,
		FormatId $formatId,
		int $rating,
		float $averageWeightPerTeam
	) {
		$this->validateMonth($month);

		if ($rating < 0) {
			throw new InvalidRatingException('Invalid rating: ' . $rating);
		}

		if ($averageWeightPerTeam < 0) {
			throw new InvalidAverageWeightPerTeamException(
				'Invalid average weight per team: ' . $averageWeightPerTeam
			);
		}

		$this->month = $month;
		$this->formatId = $formatId;
		$this->rating = $rating;
		$this->averageWeightPerTeam = $averageWeightPerTeam;
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
	 * Get the rating.
	 *
	 * @return int
	 */
	public function getRating() : int
	{
		return $this->rating;
	}

	/**
	 * Get the average weight per team.
	 *
	 * @return float
	 */
	public function getAverageWeightPerTeam() : float
	{
		return $this->averageWeightPerTeam;
	}
}
