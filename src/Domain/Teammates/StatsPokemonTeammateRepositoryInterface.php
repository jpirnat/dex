<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Teammates;

use DateTimeInterface;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\PokemonId;

interface StatsPokemonTeammateRepositoryInterface
{
    /**
     * Get stats Pokémon teammates by month, format, rating, and Pokémon.
     *
     * @return StatsPokemonTeammate[] Ordered by percent descending.
     */
    public function getByMonth(
        DateTimeInterface $month,
        FormatId $formatId,
        int $rating,
        PokemonId $pokemonId,
        LanguageId $languageId,
    ): array;
}
