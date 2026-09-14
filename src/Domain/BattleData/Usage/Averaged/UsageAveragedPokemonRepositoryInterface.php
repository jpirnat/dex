<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\BattleData\Usage\Averaged;

use DateTimeInterface;
use Jp\Dex\Domain\Formats\FormatId;

interface UsageAveragedPokemonRepositoryInterface
{
    /**
     * Get usage averaged Pokémon records by their start month, end month, and
     * format.
     *
     * @return UsageAveragedPokemon[] Indexed by Pokémon id.
     */
    public function getByMonthsAndFormat(
        DateTimeInterface $start,
        DateTimeInterface $end,
        FormatId $formatId,
    ): array;
}
