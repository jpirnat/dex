<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\BattleData\Trends\Generators;

use Jp\Dex\Domain\BattleData\ChartQueriesInterface;
use Jp\Dex\Domain\BattleData\Trends\Lines\MovesetTeraTrendLine;
use Jp\Dex\Domain\Formats\Format;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Pokemon\PokemonNameRepositoryInterface;
use Jp\Dex\Domain\Pokemon\VgPokemonRepositoryInterface;
use Jp\Dex\Domain\Types\TypeId;
use Jp\Dex\Domain\Types\TypeNameRepositoryInterface;
use Jp\Dex\Domain\Types\TypeRepositoryInterface;

final readonly class MovesetTeraTrendGenerator
{
    public function __construct(
        private ChartQueriesInterface $chartQueries,
        private PokemonNameRepositoryInterface $pokemonNameRepository,
        private TypeNameRepositoryInterface $typeNameRepository,
        private VgPokemonRepositoryInterface $vgPokemonRepository,
        private TypeRepositoryInterface $typeRepository,
        private TrendPointCalculator $trendPointCalculator,
    ) {}

    /**
     * Get the data for a moveset Tera trend line.
     */
    public function generate(
        Format $format,
        int $rating,
        PokemonId $pokemonId,
        TypeId $typeId,
        LanguageId $languageId,
    ): MovesetTeraTrendLine {
        // Get the name data.
        $pokemonName = $this->pokemonNameRepository->getByLanguageAndPokemon(
            $languageId,
            $pokemonId,
        );
        $typeName = $this->typeNameRepository->getByLanguageAndType(
            $languageId,
            $typeId,
        );

        // Get the Pokémon's primary type.
        $vgPokemon = $this->vgPokemonRepository->getByVgAndPokemon(
            $format->versionGroupId,
            $pokemonId,
        );
        $pokemonType = $this->typeRepository->getById($vgPokemon->type1Id);

        // Get the type.
        $teraType = $this->typeRepository->getById($typeId);

        // Get the usage data.
        $usageDatas = $this->chartQueries->getMovesetTera(
            $format->id,
            $rating,
            $pokemonId,
            $typeId,
        );
        $months = $this->chartQueries->getMonthsWithData($format->id, $rating);

        // Get the trend points.
        $trendPoints = $this->trendPointCalculator->getTrendPoints(
            $format->id,
            $usageDatas,
            $months,
            0,
        );

        return new MovesetTeraTrendLine(
            $format->name,
            $rating,
            $pokemonName->name,
            $typeName->name,
            $pokemonType->colorCode,
            $teraType->colorCode,
            $trendPoints,
        );
    }
}
