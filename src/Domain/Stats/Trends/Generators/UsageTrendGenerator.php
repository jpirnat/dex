<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Trends\Generators;

use Jp\Dex\Domain\Formats\Format;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Pokemon\PokemonNameRepositoryInterface;
use Jp\Dex\Domain\Pokemon\VgPokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\StatsChartQueriesInterface;
use Jp\Dex\Domain\Stats\Trends\Lines\UsageTrendLine;
use Jp\Dex\Domain\Types\TypeRepositoryInterface;

final readonly class UsageTrendGenerator
{
	public function __construct(
		private StatsChartQueriesInterface $statsChartQueries,
		private PokemonNameRepositoryInterface $pokemonNameRepository,
		private VgPokemonRepositoryInterface $vgPokemonRepository,
		private TypeRepositoryInterface $typeRepository,
		private TrendPointCalculator $trendPointCalculator,
	) {}

	/**
	 * Get the data for a lead usage trend line.
	 */
	public function generate(
		Format $format,
		int $rating,
		PokemonId $pokemonId,
		LanguageId $languageId,
	) : UsageTrendLine {
		// Get the name data.
		$pokemonName = $this->pokemonNameRepository->getByLanguageAndPokemon(
			$languageId,
			$pokemonId,
		);

		// Get the Pokémon's primary type.
		$vgPokemon = $this->vgPokemonRepository->getByVgAndPokemon(
			$format->getVersionGroupId(),
			$pokemonId,
		);
		$pokemonType = $this->typeRepository->getById($vgPokemon->getType1Id());

		// Get the usage data.
		$usageDatas = $this->statsChartQueries->getUsage(
			$format->getId(),
			$rating,
			$pokemonId,
		);
		$months = $this->statsChartQueries->getMonthsWithData($format->getId(), $rating);

		// Get the trend points.
		$trendPoints = $this->trendPointCalculator->getTrendPoints(
			$format->getId(),
			$usageDatas,
			$months,
			0,
		);

		return new UsageTrendLine(
			$format->getName(),
			$rating,
			$pokemonName,
			$pokemonType,
			$trendPoints,
		);
	}
}
