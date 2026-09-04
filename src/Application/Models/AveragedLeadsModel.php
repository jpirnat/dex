<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use DateTime;
use Jp\Dex\Domain\BattleData\Leads\Averaged\LeadsAveragedPokemonRepositoryInterface;
use Jp\Dex\Domain\BattleData\Leads\Averaged\LeadsRatedAveragedPokemonRepositoryInterface;
use Jp\Dex\Domain\BattleData\Usage\Averaged\MonthsCounter;
use Jp\Dex\Domain\BattleData\Usage\RatingQueriesInterface;
use Jp\Dex\Domain\Formats\Format;
use Jp\Dex\Domain\Formats\FormatRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Leads\AveragedLeadsPokemon;
use Jp\Dex\Domain\Pokemon\DexPokemonRepositoryInterface;

final class AveragedLeadsModel
{
    private(set) string $start;
    private(set) string $end;
    private(set) Format $format;
    private(set) int $rating;
    private(set) LanguageId $languageId;

    /** @var int[] $ratings */
    private(set) array $ratings = [];

    /** @var AveragedLeadsPokemon[] $pokemon */
    private(set) array $pokemon = [];


    public function __construct(
        private readonly FormatRepositoryInterface $formatRepository,
        private readonly RatingQueriesInterface $ratingQueries,
        private readonly LeadsAveragedPokemonRepositoryInterface $leadsAveragedPokemonRepository,
        private readonly LeadsRatedAveragedPokemonRepositoryInterface $leadsRatedAveragedPokemonRepository,
        private readonly DexPokemonRepositoryInterface $dexPokemonRepository,
        private readonly MonthsCounter $monthsCounter,
    ) {}


    /**
     * Get leads data averaged over multiple months.
     */
    public function setData(
        string $start,
        string $end,
        string $formatIdentifier,
        int $rating,
        LanguageId $languageId,
    ): void {
        $this->start = $start;
        $this->end = $end;
        $this->rating = $rating;
        $this->languageId = $languageId;

        // Get the start month and end month.
        $start = new DateTime("$start-01");
        $end = new DateTime("$end-01");

        // Get the format.
        $this->format = $this->formatRepository->getByIdentifier(
            $formatIdentifier,
            $languageId,
        );

        // Get the ratings for these months.
        $this->ratings = $this->ratingQueries->getByMonthsAndFormat(
            $start,
            $end,
            $this->format->id,
        );

        // Get leads Pokémon records for these months.
        $leadsAveragedPokemons = $this->leadsAveragedPokemonRepository->getByMonthsAndFormat(
            $start,
            $end,
            $this->format->id,
        );

        // Get leads rated Pokémon records for these months.
        $leadsRatedAveragedPokemons = $this->leadsRatedAveragedPokemonRepository->getByMonthsAndFormatAndRating(
            $start,
            $end,
            $this->format->id,
            $rating,
        );

        // Get each Pokémon's count of months with moveset data (to determine
        // whether the moveset link should be shown).
        $monthCounts = $this->monthsCounter->countMovesetMonthsAll(
            $start,
            $end,
            $this->format->id,
            $rating,
        );

        // Get each leads record's data.
        foreach ($leadsRatedAveragedPokemons as $leadsRatedAveragedPokemon) {
            $dexPokemon = $this->dexPokemonRepository->getById(
                $this->format->versionGroupId,
                $leadsRatedAveragedPokemon->pokemonId,
                $languageId,
            );

            $pokemonId = $leadsRatedAveragedPokemon->pokemonId;

            // Get this Pokémon's number of months of moveset data.
            $numberOfMonths = $monthCounts[$pokemonId->value] ?? 0;

            // Get this Pokémon's non-rated usage record for these months.
            $leadsAveragedPokemon = $leadsAveragedPokemons[$pokemonId->value];

            $this->pokemon[] = new AveragedLeadsPokemon(
                $leadsRatedAveragedPokemon->rank,
                $dexPokemon->icon,
                $numberOfMonths,
                $dexPokemon->identifier,
                $dexPokemon->name,
                $leadsRatedAveragedPokemon->usagePercent,
                $leadsAveragedPokemon->raw,
                $leadsAveragedPokemon->rawPercent,
            );
        }
    }
}
