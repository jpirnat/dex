<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Trends\Generators;

use Jp\Dex\Domain\Formats\Format;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Moves\MoveNameRepositoryInterface;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Pokemon\PokemonNameRepositoryInterface;
use Jp\Dex\Domain\Pokemon\VgPokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\StatsChartQueriesInterface;
use Jp\Dex\Domain\Stats\Trends\Lines\UsageMoveTrendLine;
use Jp\Dex\Domain\Types\TypeRepositoryInterface;

final readonly class UsageMoveTrendGenerator
{
	public function __construct(
		private StatsChartQueriesInterface $statsChartQueries,
		private PokemonNameRepositoryInterface $pokemonNameRepository,
		private MoveNameRepositoryInterface $moveNameRepository,
		private VgPokemonRepositoryInterface $vgPokemonRepository,
		private TypeRepositoryInterface $typeRepository,
		private TrendPointCalculator $trendPointCalculator,
	) {}

	/**
	 * Get the data for a usage move trend line.
	 */
	public function generate(
		Format $format,
		int $rating,
		PokemonId $pokemonId,
		MoveId $moveId,
		LanguageId $languageId,
	) : UsageMoveTrendLine {
		// Get the name data.
		$pokemonName = $this->pokemonNameRepository->getByLanguageAndPokemon(
			$languageId,
			$pokemonId,
		);
		$moveName = $this->moveNameRepository->getByLanguageAndMove(
			$languageId,
			$moveId,
		);

		// Get the Pokémon's primary type.
		$vgPokemon = $this->vgPokemonRepository->getByVgAndPokemon(
			$format->versionGroupId,
			$pokemonId,
		);
		$pokemonType = $this->typeRepository->getById($vgPokemon->getType1Id());

		// Get the usage data.
		$usageDatas = $this->statsChartQueries->getUsageMove(
			$format->id,
			$rating,
			$pokemonId,
			$moveId,
		);
		$months = $this->statsChartQueries->getMonthsWithData($format->id, $rating);

		// Get the trend points.
		$trendPoints = $this->trendPointCalculator->getTrendPoints(
			$format->id,
			$usageDatas,
			$months,
			0,
		);

		return new UsageMoveTrendLine(
			$format->name,
			$rating,
			$pokemonName,
			$moveName,
			$pokemonType,
			$trendPoints,
		);
	}
}
