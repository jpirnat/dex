<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\BattleData\Moveset;

use Jp\Dex\Domain\BattleData\Exceptions\InvalidPercentException;
use Jp\Dex\Domain\BattleData\Usage\UsageRatedPokemonId;
use Jp\Dex\Domain\Pokemon\PokemonId;

final readonly class MovesetRatedTeammate
{
    /**
     * Constructor.
     *
     * @throws InvalidPercentException if $percent is invalid
     */
    public function __construct(
        private(set) UsageRatedPokemonId $usageRatedPokemonId,
        private(set) PokemonId $teammateId,
        private(set) float $percent,
    ) {
        if ($percent < 0 || $percent > 100) {
            throw new InvalidPercentException("Invalid percent: $percent.");
        }
    }
}
