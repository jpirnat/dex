<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Leads;

use DateTimeInterface;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Languages\LanguageId;

interface StatsLeadsPokemonRepositoryInterface
{
    /**
     * Get stats leads Pokémon by month, format, and rating.
     *
     * @return StatsLeadsPokemon[] Ordered by rank ascending.
     */
    public function getByMonth(
        DateTimeInterface $month,
        ?DateTimeInterface $prevMonth,
        FormatId $formatId,
        int $rating,
        LanguageId $languageId,
    ): array;
}
