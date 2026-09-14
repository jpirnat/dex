<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\BattleData\Usage\Averaged;

use DateTimeInterface;
use Jp\Dex\Domain\Formats\FormatId;

interface UsageRatedAveragedPokemonRepositoryInterface
{
    /**
     * Get usage rated averaged Pokémon records by their start month, end month,
     * format, and rating.
     *
     * @return UsageRatedAveragedPokemon[] Indexed by Pokémon id.
     */
    public function getByMonthsAndFormatAndRating(
        DateTimeInterface $start,
        DateTimeInterface $end,
        FormatId $formatId,
        int $rating,
    ): array;
}
