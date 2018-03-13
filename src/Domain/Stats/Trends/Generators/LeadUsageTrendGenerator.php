<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Trends\Generators;

use Jp\Dex\Domain\Formats\Format;
use Jp\Dex\Domain\Formats\FormatNameRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Pokemon\PokemonNameRepositoryInterface;
use Jp\Dex\Domain\Stats\Leads\LeadsRatedPokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\Trends\Lines\LeadUsageTrendLine;
use Jp\Dex\Domain\Types\TypeRepositoryInterface;

class LeadUsageTrendGenerator
{
	/** @var LeadsRatedPokemonRepositoryInterface $leadsRatedPokemonRepository */
	private $leadsRatedPokemonRepository;

	/** @var FormatNameRepositoryInterface $formatNameRepository */
	private $formatNameRepository;

	/** @var PokemonNameRepositoryInterface $pokemonNameRepository */
	private $pokemonNameRepository;

	/** @var TypeRepositoryInterface $typeRepository */
	private $typeRepository;

	/** @var TrendPointCalculator $trendPointCalculator */
	private $trendPointCalculator;

	/**
	 * Constructor.
	 *
	 * @param LeadsRatedPokemonRepositoryInterface $leadsRatedPokemonRepository
	 * @param FormatNameRepositoryInterface $formatNameRepository
	 * @param PokemonNameRepositoryInterface $pokemonNameRepository
	 * @param TypeRepositoryInterface $typeRepository
	 * @param TrendPointCalculator $trendPointCalculator
	 */
	public function __construct(
		LeadsRatedPokemonRepositoryInterface $leadsRatedPokemonRepository,
		FormatNameRepositoryInterface $formatNameRepository,
		PokemonNameRepositoryInterface $pokemonNameRepository,
		TypeRepositoryInterface $typeRepository,
		TrendPointCalculator $trendPointCalculator
	) {
		$this->leadsRatedPokemonRepository = $leadsRatedPokemonRepository;
		$this->formatNameRepository = $formatNameRepository;
		$this->pokemonNameRepository = $pokemonNameRepository;
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
		$formatName = $this->formatNameRepository->getByLanguageAndFormat(
			$languageId,
			$format->getId()
		);
		$pokemonName = $this->pokemonNameRepository->getByLanguageAndPokemon(
			$languageId,
			$pokemonId
		);

		// Get the Pokémon's primary type.
		$types = $this->typeRepository->getByGenerationAndPokemon(
			$format->getGeneration(),
			$pokemonId
		);
		$pokemonType = $types[1];

		// Get the usage data.
		$leadsRatedPokemons = $this->leadsRatedPokemonRepository->getByFormatAndRatingAndPokemon(
			$format->getId(),
			$rating,
			$pokemonId
		);

		// Get the trend points.
		$trendPoints = $this->trendPointCalculator->getTrendPoints(
			$format->getId(),
			$leadsRatedPokemons,
			'getUsagePercent',
			0
		);

		return new LeadUsageTrendLine(
			$formatName,
			$rating,
			$pokemonName,
			$pokemonType,
			$trendPoints
		);
	}
}
