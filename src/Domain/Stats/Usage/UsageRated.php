<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Usage;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Stats\Exceptions\InvalidAverageWeightPerTeamException;
use Jp\Dex\Domain\Stats\Exceptions\InvalidMonthException;
use Jp\Dex\Domain\Stats\Exceptions\InvalidRatingException;
use Jp\Dex\Domain\Stats\ValidateMonthTrait;

final readonly class UsageRated
{
	use ValidateMonthTrait;

	/**
	 * Constructor.
	 *
	 * @throws InvalidMonthException if $month is invalid.
	 * @throws InvalidRatingException if $rating is invalid.
	 * @throws InvalidAverageWeightPerTeamException if $averageWeightPerTeam is
	 *     invalid.
	 */
	public function __construct(
		private(set) DateTime $month,
		private(set) FormatId $formatId,
		private(set) int $rating,
		private(set) float $averageWeightPerTeam,
	) {
		$this->validateMonth($month);

		if ($rating < 0) {
			throw new InvalidRatingException("Invalid rating: $rating.");
		}

		if ($averageWeightPerTeam < 0) {
			throw new InvalidAverageWeightPerTeamException(
				"Invalid average weight per team: $averageWeightPerTeam."
			);
		}
	}
}
