<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Trends\Generators;

use Jp\Dex\Domain\Formats\Format;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Pokemon\PokemonNameRepositoryInterface;
use Jp\Dex\Domain\Stats\StatsChartQueriesInterface;
use Jp\Dex\Domain\Stats\Trends\Lines\LeadUsageTrendLine;
use Jp\Dex\Domain\Types\PokemonTypeRepositoryInterface;
use Jp\Dex\Domain\Types\TypeRepositoryInterface;

final class LeadUsageTrendGenerator
{
	private StatsChartQueriesInterface $statsChartQueries;
	private PokemonNameRepositoryInterface $pokemonNameRepository;
	private PokemonTypeRepositoryInterface $pokemonTypeRepository;
	private TypeRepositoryInterface $typeRepository;
	private TrendPointCalculator $trendPointCalculator;

	/**
	 * Constructor.
	 *
	 * @param StatsChartQueriesInterface $statsChartQueries
	 * @param PokemonNameRepositoryInterface $pokemonNameRepository
	 * @param PokemonTypeRepositoryInterface $pokemonTypeRepository
	 * @param TypeRepositoryInterface $typeRepository
	 * @param TrendPointCalculator $trendPointCalculator
	 */
	public function __construct(
		StatsChartQueriesInterface $statsChartQueries,
		PokemonNameRepositoryInterface $pokemonNameRepository,
		PokemonTypeRepositoryInterface $pokemonTypeRepository,
		TypeRepositoryInterface $typeRepository,
		TrendPointCalculator $trendPointCalculator
	) {
		$this->statsChartQueries = $statsChartQueries;
		$this->pokemonNameRepository = $pokemonNameRepository;
		$this->pokemonTypeRepository = $pokemonTypeRepository;
		$this->typeRepository = $typeRepository;
		$this->trendPointCalculator = $trendPointCalculator;
	}

	/**
	 * Get the data for a lead usage trend line.
	 *
	 * @param Format $format
	 * @param int $rating
	 * @param PokemonId $pokemonId
	 * @param LanguageId $languageId
	 *
	 * @return LeadUsageTrendLine
	 */
	public function generate(
		Format $format,
		int $rating,
		PokemonId $pokemonId,
		LanguageId $languageId
	) : LeadUsageTrendLine {
		// Get the name data.
		$pokemonName = $this->pokemonNameRepository->getByLanguageAndPokemon(
			$languageId,
			$pokemonId
		);

		// Get the Pokémon's primary type.
		$pokemonTypes = $this->pokemonTypeRepository->getByGenerationAndPokemon(
			$format->getGenerationId(),
			$pokemonId
		);
		$pokemonType = $this->typeRepository->getById($pokemonTypes[1]->getTypeId());

		// Get the usage data.
		$usageDatas = $this->statsChartQueries->getLeadUsage(
			$format->getId(),
			$rating,
			$pokemonId
		);

		// Get the trend points.
		$trendPoints = $this->trendPointCalculator->getTrendPoints(
			$format->getId(),
			$usageDatas,
			0
		);

		return new LeadUsageTrendLine(
			$format->getName(),
			$rating,
			$pokemonName,
			$pokemonType,
			$trendPoints
		);
	}
}
