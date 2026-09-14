<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\BattleData\Leads\Averaged;

use DateTimeInterface;
use Jp\Dex\Domain\Formats\FormatId;

interface LeadsRatedAveragedPokemonRepositoryInterface
{
    /**
     * Do any leads rated averaged Pokémon records exist for this start month,
     * end month, format, and rating?
     */
    public function hasAny(
        DateTimeInterface $start,
        DateTimeInterface $end,
        FormatId $formatId,
        int $rating,
    ): bool;

    /**
     * Get leads rated averaged Pokémon records by their start month, end month,
     * format, and rating.
     *
     * @return LeadsRatedAveragedPokemon[] Indexed by Pokémon id.
     */
    public function getByMonthsAndFormatAndRating(
        DateTimeInterface $start,
        DateTimeInterface $end,
        FormatId $formatId,
        int $rating,
    ): array;
}
