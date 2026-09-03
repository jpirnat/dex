<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Usage;

use DateTimeInterface;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Items\ItemId;
use Jp\Dex\Domain\Languages\LanguageId;

interface StatsItemPokemonRepositoryInterface
{
    /**
     * Get stats item Pokémon by month, format, rating, and item.
     *
     * @return StatsItemPokemon[] Ordered by usage percent descending.
     */
    public function getByMonth(
        DateTimeInterface $month,
        ?DateTimeInterface $prevMonth,
        FormatId $formatId,
        int $rating,
        ItemId $itemId,
        LanguageId $languageId,
    ): array;
}
