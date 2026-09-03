<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Usage;

use DateTimeInterface;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Moves\MoveId;

interface StatsMovePokemonRepositoryInterface
{
    /**
     * Get stats move Pokémon by month, format, rating, and move.
     *
     * @return StatsMovePokemon[] Ordered by usage percent descending.
     */
    public function getByMonth(
        DateTimeInterface $month,
        ?DateTimeInterface $prevMonth,
        FormatId $formatId,
        int $rating,
        MoveId $moveId,
        LanguageId $languageId,
    ): array;
}
