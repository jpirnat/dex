<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Trends\Generators;

use Jp\Dex\Domain\Formats\Format;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Moves\VgMoveRepositoryInterface;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Pokemon\PokemonNameRepositoryInterface;
use Jp\Dex\Domain\Stats\StatsChartQueriesInterface;
use Jp\Dex\Domain\Stats\Trends\Lines\MovesetTeraTrendLine;
use Jp\Dex\Domain\Types\PokemonTypeRepositoryInterface;
use Jp\Dex\Domain\Types\TypeId;
use Jp\Dex\Domain\Types\TypeNameRepositoryInterface;
use Jp\Dex\Domain\Types\TypeRepositoryInterface;

final readonly class MovesetTeraTrendGenerator
{
	public function __construct(
		private StatsChartQueriesInterface $statsChartQueries,
		private PokemonNameRepositoryInterface $pokemonNameRepository,
		private TypeNameRepositoryInterface $typeNameRepository,
		private PokemonTypeRepositoryInterface $pokemonTypeRepository,
		private TypeRepositoryInterface $typeRepository,
		private VgMoveRepositoryInterface $vgMoveRepository,
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
	) : MovesetTeraTrendLine {
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
		$pokemonTypes = $this->pokemonTypeRepository->getByVgAndPokemon(
			$format->getVersionGroupId(),
			$pokemonId,
		);
		$pokemonType = $this->typeRepository->getById($pokemonTypes[1]->getTypeId());

		// Get the type.
		$type = $this->typeRepository->getById($typeId);

		// Get the usage data.
		$usageDatas = $this->statsChartQueries->getMovesetTera(
			$format->getId(),
			$rating,
			$pokemonId,
			$typeId,
		);
		$months = $this->statsChartQueries->getMonthsWithData($format->getId(), $rating);

		// Get the trend points.
		$trendPoints = $this->trendPointCalculator->getTrendPoints(
			$format->getId(),
			$usageDatas,
			$months,
			0,
		);

		return new MovesetTeraTrendLine(
			$format->getName(),
			$rating,
			$pokemonName,
			$typeName->getName(),
			$pokemonType,
			$type->getColorCode(),
			$trendPoints,
		);
	}
}
