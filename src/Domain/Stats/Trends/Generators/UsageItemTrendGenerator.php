<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Trends\Generators;

use Jp\Dex\Domain\Formats\Format;
use Jp\Dex\Domain\Items\ItemId;
use Jp\Dex\Domain\Items\ItemNameRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Pokemon\PokemonNameRepositoryInterface;
use Jp\Dex\Domain\Pokemon\VgPokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\StatsChartQueriesInterface;
use Jp\Dex\Domain\Stats\Trends\Lines\UsageItemTrendLine;
use Jp\Dex\Domain\Types\TypeRepositoryInterface;

final readonly class UsageItemTrendGenerator
{
	public function __construct(
		private StatsChartQueriesInterface $statsChartQueries,
		private PokemonNameRepositoryInterface $pokemonNameRepository,
		private ItemNameRepositoryInterface $itemNameRepository,
		private VgPokemonRepositoryInterface $vgPokemonRepository,
		private TypeRepositoryInterface $typeRepository,
		private TrendPointCalculator $trendPointCalculator,
	) {}

	/**
	 * Get the data for a usage item trend line.
	 */
	public function generate(
		Format $format,
		int $rating,
		PokemonId $pokemonId,
		ItemId $itemId,
		LanguageId $languageId,
	) : UsageItemTrendLine {
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
		$vgPokemon = $this->vgPokemonRepository->getByVgAndPokemon(
			$format->versionGroupId,
			$pokemonId,
		);
		$pokemonType = $this->typeRepository->getById($vgPokemon->type1Id);

		// Get the usage data.
		$usageDatas = $this->statsChartQueries->getUsageItem(
			$format->id,
			$rating,
			$pokemonId,
			$itemId,
		);
		$months = $this->statsChartQueries->getMonthsWithData($format->id, $rating);

		// Get the trend points.
		$trendPoints = $this->trendPointCalculator->getTrendPoints(
			$format->id,
			$usageDatas,
			$months,
			0,
		);

		return new UsageItemTrendLine(
			$format->name,
			$rating,
			$pokemonName->name,
			$itemName->name,
			$pokemonType->colorCode,
			$trendPoints,
		);
	}
}
