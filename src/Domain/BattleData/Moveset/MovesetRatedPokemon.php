<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\BattleData\Moveset;

use Jp\Dex\Domain\BattleData\Exceptions\InvalidAverageWeightException;
use Jp\Dex\Domain\BattleData\Usage\UsageRatedPokemonId;

final readonly class MovesetRatedPokemon
{
    /**
     * Constructor.
     *
     * @throws InvalidAverageWeightException if $averageWeight is invalid.
     */
    public function __construct(
        private(set) UsageRatedPokemonId $usageRatedPokemonId,
        private(set) float $averageWeight,
    ) {
        if ($averageWeight < 0) {
            throw new InvalidAverageWeightException(
                "Invalid average weight: $averageWeight."
            );
        }
    }
}
