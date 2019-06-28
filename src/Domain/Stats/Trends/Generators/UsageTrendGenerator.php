<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Trends\Generators;

use Jp\Dex\Domain\Formats\Format;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Pokemon\PokemonNameRepositoryInterface;
use Jp\Dex\Domain\Stats\Trends\Lines\UsageTrendLine;
use Jp\Dex\Domain\Stats\Usage\UsageRatedPokemonRepositoryInterface;
use Jp\Dex\Domain\Types\PokemonTypeRepositoryInterface;
use Jp\Dex\Domain\Types\TypeRepositoryInterface;

class UsageTrendGenerator
{
	/** @var UsageRatedPokemonRepositoryInterface $usageRatedPokemonRepository */
	private $usageRatedPokemonRepository;

	/** @var PokemonNameRepositoryInterface $pokemonNameRepository */
	private $pokemonNameRepository;

	/** @var PokemonTypeRepositoryInterface $pokemonTypeRepository */
	private $pokemonTypeRepository;

	/** @var TypeRepositoryInterface $typeRepository */
	private $typeRepository;

	/** @var TrendPointCalculator $trendPointCalculator */
	private $trendPointCalculator;

	/**
	 * Constructor.
	 *
	 * @param UsageRatedPokemonRepositoryInterface $usageRatedPokemonRepository
	 * @param PokemonNameRepositoryInterface $pokemonNameRepository
	 * @param PokemonTypeRepositoryInterface $pokemonTypeRepository
	 * @param TypeRepositoryInterface $typeRepository
	 * @param TrendPointCalculator $trendPointCalculator
	 */
	public function __construct(
		UsageRatedPokemonRepositoryInterface $usageRatedPokemonRepository,
		PokemonNameRepositoryInterface $pokemonNameRepository,
		PokemonTypeRepositoryInterface $pokemonTypeRepository,
		TypeRepositoryInterface $typeRepository,
		TrendPointCalculator $trendPointCalculator
	) {
		$this->usageRatedPokemonRepository = $usageRatedPokemonRepository;
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
	 * @return UsageTrendLine
	 */
	public function generate(
		Format $format,
		int $rating,
		PokemonId $pokemonId,
		LanguageId $languageId
	) : UsageTrendLine {
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
		$usageRatedPokemons = $this->usageRatedPokemonRepository->getByFormatAndRatingAndPokemon(
			$format->getId(),
			$rating,
			$pokemonId
		);

		// Get the trend points.
		$trendPoints = $this->trendPointCalculator->getTrendPoints(
			$format->getId(),
			$usageRatedPokemons,
			'getUsagePercent',
			0
		);

		return new UsageTrendLine(
			$format->getName(),
			$rating,
			$pokemonName,
			$pokemonType,
			$trendPoints
		);
	}
}
