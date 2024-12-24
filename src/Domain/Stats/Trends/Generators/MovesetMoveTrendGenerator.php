<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Trends\Generators;

use Jp\Dex\Domain\Formats\Format;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Moves\MoveNameRepositoryInterface;
use Jp\Dex\Domain\Moves\VgMoveRepositoryInterface;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Pokemon\PokemonNameRepositoryInterface;
use Jp\Dex\Domain\Pokemon\VgPokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\StatsChartQueriesInterface;
use Jp\Dex\Domain\Stats\Trends\Lines\MovesetMoveTrendLine;
use Jp\Dex\Domain\Types\TypeRepositoryInterface;

final readonly class MovesetMoveTrendGenerator
{
	public function __construct(
		private StatsChartQueriesInterface $statsChartQueries,
		private PokemonNameRepositoryInterface $pokemonNameRepository,
		private MoveNameRepositoryInterface $moveNameRepository,
		private VgPokemonRepositoryInterface $vgPokemonRepository,
		private TypeRepositoryInterface $typeRepository,
		private VgMoveRepositoryInterface $vgMoveRepository,
		private TrendPointCalculator $trendPointCalculator,
	) {}

	/**
	 * Get the data for a moveset move trend line.
	 */
	public function generate(
		Format $format,
		int $rating,
		PokemonId $pokemonId,
		MoveId $moveId,
		LanguageId $languageId,
	) : MovesetMoveTrendLine {
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
		$pokemonType = $this->typeRepository->getById($vgPokemon->type1Id);

		// Get the move's type.
		$vgMove = $this->vgMoveRepository->getByVgAndMove(
			$format->versionGroupId,
			$moveId,
		);
		$moveType = $this->typeRepository->getById($vgMove->typeId);

		// Get the usage data.
		$usageDatas = $this->statsChartQueries->getMovesetMove(
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

		return new MovesetMoveTrendLine(
			$format->name,
			$rating,
			$pokemonName->name,
			$moveName->name,
			$pokemonType->colorCode,
			$moveType->colorCode,
			$trendPoints,
		);
	}
}
