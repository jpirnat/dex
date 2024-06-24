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
use Jp\Dex\Domain\Stats\Trends\Lines\MovesetItemTrendLine;
use Jp\Dex\Domain\Types\PokemonTypeRepositoryInterface;
use Jp\Dex\Domain\Types\TypeRepositoryInterface;

final readonly class MovesetItemTrendGenerator
{
	public function __construct(
		private StatsChartQueriesInterface $statsChartQueries,
		private PokemonNameRepositoryInterface $pokemonNameRepository,
		private ItemNameRepositoryInterface $itemNameRepository,
		private PokemonTypeRepositoryInterface $pokemonTypeRepository,
		private TypeRepositoryInterface $typeRepository,
		private TrendPointCalculator $trendPointCalculator,
	) {}

	/**
	 * Get the data for a moveset item trend line.
	 */
	public function generate(
		Format $format,
		int $rating,
		PokemonId $pokemonId,
		ItemId $itemId,
		LanguageId $languageId,
	) : MovesetItemTrendLine {
		// Get the name data.
		$pokemonName = $this->pokemonNameRepository->getByLanguageAndPokemon(
			$languageId,
			$pokemonId,
		);
		$itemName = $this->itemNameRepository->getByLanguageAndItem(
			$languageId,
			$itemId,
		);

		// Get the Pokémon's primary type.
		$pokemonTypes = $this->pokemonTypeRepository->getByVgAndPokemon(
			$format->getVersionGroupId(),
			$pokemonId,
		);
		$pokemonType = $this->typeRepository->getById($pokemonTypes[1]->getTypeId());

		// Get the usage data.
		$usageDatas = $this->statsChartQueries->getMovesetItem(
			$format->getId(),
			$rating,
			$pokemonId,
			$itemId,
		);
		$months = $this->statsChartQueries->getMonthsWithData($format->getId(), $rating);

		// Get the trend points.
		$trendPoints = $this->trendPointCalculator->getTrendPoints(
			$format->getId(),
			$usageDatas,
			$months,
			0,
		);

		return new MovesetItemTrendLine(
			$format->getName(),
			$rating,
			$pokemonName,
			$itemName,
			$pokemonType,
			$trendPoints,
		);
	}
}
