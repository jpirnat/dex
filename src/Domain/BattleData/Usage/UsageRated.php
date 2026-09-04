<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\BattleData\Usage;

use DateTimeImmutable;
use Jp\Dex\Domain\BattleData\Exceptions\InvalidAverageWeightPerTeamException;
use Jp\Dex\Domain\BattleData\Exceptions\InvalidMonthException;
use Jp\Dex\Domain\BattleData\Exceptions\InvalidRatingException;
use Jp\Dex\Domain\BattleData\ValidateMonthTrait;
use Jp\Dex\Domain\Formats\FormatId;

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
        private(set) DateTimeImmutable $month,
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
