<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\BattleData\Moveset;

use Jp\Dex\Domain\BattleData\Exceptions\InvalidPercentException;
use Jp\Dex\Domain\BattleData\Usage\UsageRatedPokemonId;
use Jp\Dex\Domain\Moves\MoveId;

final readonly class MovesetRatedMove
{
    /**
     * Constructor.
     *
     * @throws InvalidPercentException if $percent is invalid
     */
    public function __construct(
        private(set) UsageRatedPokemonId $usageRatedPokemonId,
        private(set) MoveId $moveId,
        private(set) float $percent,
    ) {
        if ($percent < 0 || $percent > 100) {
            throw new InvalidPercentException("Invalid percent: $percent.");
        }
    }
}
