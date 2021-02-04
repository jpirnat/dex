<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Trends\Generators;

use Jp\Dex\Domain\Formats\Format;
use Jp\Dex\Domain\Items\ItemId;
use Jp\Dex\Domain\Items\ItemNameRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Pokemon\PokemonNameRepositoryInterface;
use Jp\Dex\Domain\Stats\StatsChartQueriesInterface;
use Jp\Dex\Domain\Stats\Trends\Lines\UsageItemTrendLine;
use Jp\Dex\Domain\Types\PokemonTypeRepositoryInterface;
use Jp\Dex\Domain\Types\TypeRepositoryInterface;

final class UsageItemTrendGenerator
{
	private StatsChartQueriesInterface $statsChartQueries;
	private PokemonNameRepositoryInterface $pokemonNameRepository;
	private ItemNameRepositoryInterface $itemNameRepository;
	private PokemonTypeRepositoryInterface $pokemonTypeRepository;
	private TypeRepositoryInterface $typeRepository;
	private TrendPointCalculator $trendPointCalculator;

	/**
	 * Constructor.
	 *
	 * @param StatsChartQueriesInterface $statsChartQueries
	 * @param PokemonNameRepositoryInterface $pokemonNameRepository
	 * @param ItemNameRepositoryInterface $itemNameRepository
	 * @param PokemonTypeRepositoryInterface $pokemonTypeRepository
	 * @param TypeRepositoryInterface $typeRepository
	 * @param TrendPointCalculator $trendPointCalculator
	 */
	public function __construct(
		StatsChartQueriesInterface $statsChartQueries,
		PokemonNameRepositoryInterface $pokemonNameRepository,
		ItemNameRepositoryInterface $itemNameRepository,
		PokemonTypeRepositoryInterface $pokemonTypeRepository,
		TypeRepositoryInterface $typeRepository,
		TrendPointCalculator $trendPointCalculator
	) {
		$this->statsChartQueries = $statsChartQueries;
		$this->pokemonNameRepository = $pokemonNameRepository;
		$this->itemNameRepository = $itemNameRepository;
		$this->pokemonTypeRepository = $pokemonTypeRepository;
		$this->typeRepository = $typeRepository;
		$this->trendPointCalculator = $trendPointCalculator;
	}

	/**
	 * Get the data for a usage item trend line.
	 *
	 * @param Format $format
	 * @param int $rating
	 * @param PokemonId $pokemonId
	 * @param ItemId $itemId
	 * @param LanguageId $languageId
	 *
	 * @return UsageItemTrendLine
	 */
	public function generate(
		Format $format,
		int $rating,
		PokemonId $pokemonId,
		ItemId $itemId,
		LanguageId $languageId
	) : UsageItemTrendLine {
		// Get the name data.
		$pokemonName = $this->pokemonNameRepository->getByLanguageAndPokemon(
			$languageId,
			$pokemonId
		);
		$itemName = $this->itemNameRepository->getByLanguageAndItem(
			$languageId,
			$itemId
		);

		// Get the Pokémon's primary type.
		$pokemonTypes = $this->pokemonTypeRepository->getByGenerationAndPokemon(
			$format->getGenerationId(),
			$pokemonId
		);
		$pokemonType = $this->typeRepository->getById($pokemonTypes[1]->getTypeId());

		// Get the usage data.
		$usageDatas = $this->statsChartQueries->getUsageItem(
			$format->getId(),
			$rating,
			$pokemonId,
			$itemId
		);
		$months = $this->statsChartQueries->getMonthsWithData($format->getId(), $rating);

		// Get the trend points.
		$trendPoints = $this->trendPointCalculator->getTrendPoints(
			$format->getId(),
			$usageDatas,
			$months,
			0
		);

		return new UsageItemTrendLine(
			$format->getName(),
			$rating,
			$pokemonName,
			$itemName,
			$pokemonType,
			$trendPoints
		);
	}
}
