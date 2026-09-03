<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Usage;

use DateTimeInterface;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\PokemonId;

interface StatsUsagePokemonRepositoryInterface
{
    /**
     * Get stats usage Pokémon by month, format, and rating.
     *
     * @return StatsUsagePokemon[] Ordered by rank ascending.
     */
    public function getByMonth(
        DateTimeInterface $month,
        ?DateTimeInterface $prevMonth,
        FormatId $formatId,
        int $rating,
        LanguageId $languageId,
    ): array;

    /**
     * Get a stats usage Pokémon by month, format, rating, and Pokémon id.
     */
    public function getByPokemon(
        DateTimeInterface $month,
        FormatId $formatId,
        int $rating,
        PokemonId $pokemonId,
        LanguageId $languageId,
    ): ?array;

    /**
     * Get a stats usage Pokémon by month, format, rating, and rank.
     */
    public function getByRank(
        DateTimeInterface $month,
        FormatId $formatId,
        int $rating,
        int $rank,
        LanguageId $languageId,
    ): ?array;
}
