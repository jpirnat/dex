<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\BattleData\Moveset\Averaged;

use DateTime;
use Jp\Dex\Domain\BattleData\Exceptions\InvalidMonthException;
use Jp\Dex\Domain\BattleData\Exceptions\InvalidPercentException;
use Jp\Dex\Domain\BattleData\Exceptions\InvalidRatingException;
use Jp\Dex\Domain\BattleData\ValidateMonthTrait;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Pokemon\PokemonId;

/**
 * This class holds data derived from averaging a move's usage percent over a
 * span of multiple months.
 */
final readonly class MovesetRatedAveragedMove
{
    use ValidateMonthTrait;

    /**
     * Constructor.
     *
     * @throws InvalidMonthException if $start or $end is invalid.
     * @throws InvalidRatingException if $rating is invalid.
     * @throws InvalidPercentException if $percent is invalid
     */
    public function __construct(
        private(set) DateTime $start,
        private(set) DateTime $end,
        private(set) FormatId $formatId,
        private(set) int $rating,
        private(set) PokemonId $pokemonId,
        private(set) MoveId $moveId,
        private(set) float $percent,
    ) {
        $this->validateMonth($start);
        $this->validateMonth($end);

        if ($rating < 0) {
            throw new InvalidRatingException("Invalid rating: $rating.");
        }

        if ($percent < 0 || $percent > 100) {
            throw new InvalidPercentException("Invalid percent: $percent.");
        }
    }
}
