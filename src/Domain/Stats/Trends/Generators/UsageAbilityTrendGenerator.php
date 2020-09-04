<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Trends\Generators;

use Jp\Dex\Domain\Abilities\AbilityId;
use Jp\Dex\Domain\Abilities\AbilityNameRepositoryInterface;
use Jp\Dex\Domain\Formats\Format;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Pokemon\PokemonNameRepositoryInterface;
use Jp\Dex\Domain\Stats\StatsChartQueriesInterface;
use Jp\Dex\Domain\Stats\Trends\Lines\UsageAbilityTrendLine;
use Jp\Dex\Domain\Types\PokemonTypeRepositoryInterface;
use Jp\Dex\Domain\Types\TypeRepositoryInterface;

final class UsageAbilityTrendGenerator
{
	private StatsChartQueriesInterface $statsChartQueries;
	private PokemonNameRepositoryInterface $pokemonNameRepository;
	private AbilityNameRepositoryInterface $abilityNameRepository;
	private PokemonTypeRepositoryInterface $pokemonTypeRepository;
	private TypeRepositoryInterface $typeRepository;
	private TrendPointCalculator $trendPointCalculator;

	/**
	 * Constructor.
	 *
	 * @param StatsChartQueriesInterface $statsChartQueries
	 * @param PokemonNameRepositoryInterface $pokemonNameRepository
	 * @param AbilityNameRepositoryInterface $abilityNameRepository
	 * @param PokemonTypeRepositoryInterface $pokemonTypeRepository
	 * @param TypeRepositoryInterface $typeRepository
	 * @param TrendPointCalculator $trendPointCalculator
	 */
	public function __construct(
		StatsChartQueriesInterface $statsChartQueries,
		PokemonNameRepositoryInterface $pokemonNameRepository,
		AbilityNameRepositoryInterface $abilityNameRepository,
		PokemonTypeRepositoryInterface $pokemonTypeRepository,
		TypeRepositoryInterface $typeRepository,
		TrendPointCalculator $trendPointCalculator
	) {
		$this->statsChartQueries = $statsChartQueries;
		$this->pokemonNameRepository = $pokemonNameRepository;
		$this->abilityNameRepository = $abilityNameRepository;
		$this->pokemonTypeRepository = $pokemonTypeRepository;
		$this->typeRepository = $typeRepository;
		$this->trendPointCalculator = $trendPointCalculator;
	}

	/**
	 * Get the data for a usage ability trend line.
	 *
	 * @param Format $format
	 * @param int $rating
	 * @param PokemonId $pokemonId
	 * @param AbilityId $abilityId
	 * @param LanguageId $languageId
	 *
	 * @return UsageAbilityTrendLine
	 */
	public function generate(
		Format $format,
		int $rating,
		PokemonId $pokemonId,
		AbilityId $abilityId,
		LanguageId $languageId
	) : UsageAbilityTrendLine {
		// Get the name data.
		$pokemonName = $this->pokemonNameRepository->getByLanguageAndPokemon(
			$languageId,
			$pokemonId
		);
		$abilityName = $this->abilityNameRepository->getByLanguageAndAbility(
			$languageId,
			$abilityId
		);

		// Get the Pokémon's primary type.
		$pokemonTypes = $this->pokemonTypeRepository->getByGenerationAndPokemon(
			$format->getGenerationId(),
			$pokemonId
		);
		$pokemonType = $this->typeRepository->getById($pokemonTypes[1]->getTypeId());

		// Get the usage data.
		$usageDatas = $this->statsChartQueries->getUsageAbility(
			$format->getId(),
			$rating,
			$pokemonId,
			$abilityId
		);

		// Get the trend points.
		$trendPoints = $this->trendPointCalculator->getTrendPoints(
			$format->getId(),
			$usageDatas,
			0
		);

		return new UsageAbilityTrendLine(
			$format->getName(),
			$rating,
			$pokemonName,
			$abilityName,
			$pokemonType,
			$trendPoints
		);
	}
}
