<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\BattleData\Leads\Averaged;

use DateTime;
use Jp\Dex\Domain\BattleData\Exceptions\InvalidMonthException;
use Jp\Dex\Domain\BattleData\Exceptions\InvalidPercentException;
use Jp\Dex\Domain\BattleData\Exceptions\InvalidRankException;
use Jp\Dex\Domain\BattleData\Exceptions\InvalidRatingException;
use Jp\Dex\Domain\BattleData\ValidateMonthTrait;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Pokemon\PokemonId;

/**
 * This class holds data derived from averaging a Pokémon's leads rated Pokémon
 * data over a span of multiple months.
 */
final readonly class LeadsRatedAveragedPokemon
{
    use ValidateMonthTrait;

    /**
     * Constructor.
     *
     * @throws InvalidMonthException if $start or $end is invalid.
     * @throws InvalidRatingException if $rating is invalid.
     * @throws InvalidRankException if $rank is invalid.
     * @throws InvalidPercentException if $usagePercent is invalid
     */
    public function __construct(
        private(set) DateTime $start,
        private(set) DateTime $end,
        private(set) FormatId $formatId,
        private(set) int $rating,
        private(set) PokemonId $pokemonId,
        private(set) int $rank,
        private(set) float $usagePercent,
    ) {
        $this->validateMonth($start);
        $this->validateMonth($end);

        if ($rating < 0) {
            throw new InvalidRatingException("Invalid rating: $rating.");
        }

        if ($rank < 1) {
            throw new InvalidRankException("Invalid rank: $rank.");
        }

        if ($usagePercent < 0 || $usagePercent > 100) {
            throw new InvalidPercentException("Invalid usage percent: $usagePercent.");
        }
    }
}
