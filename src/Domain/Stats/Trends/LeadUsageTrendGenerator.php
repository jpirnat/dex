<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Trends;

use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Formats\FormatNameRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Pokemon\PokemonNameRepositoryInterface;
use Jp\Dex\Domain\Stats\Leads\LeadsRatedPokemonRepositoryInterface;

class LeadUsageTrendGenerator
{
	/** @var LeadsRatedPokemonRepositoryInterface $leadsRatedPokemonRepository */
	private $leadsRatedPokemonRepository;

	/** @var FormatNameRepositoryInterface $formatNameRepository */
	private $formatNameRepository;

	/** @var PokemonNameRepositoryInterface $pokemonNameRepository */
	private $pokemonNameRepository;

	/** @var TrendPointCalculator $trendPointCalculator */
	private $trendPointCalculator;

	/**
	 * Constructor.
	 *
	 * @param LeadsRatedPokemonRepositoryInterface $leadsRatedPokemonRepository
	 * @param FormatNameRepositoryInterface $formatNameRepository
	 * @param PokemonNameRepositoryInterface $pokemonNameRepository
	 * @param TrendPointCalculator $trendPointCalculator
	 */
	public function __construct(
		LeadsRatedPokemonRepositoryInterface $leadsRatedPokemonRepository,
		FormatNameRepositoryInterface $formatNameRepository,
		PokemonNameRepositoryInterface $pokemonNameRepository,
		TrendPointCalculator $trendPointCalculator
	) {
		$this->leadsRatedPokemonRepository = $leadsRatedPokemonRepository;
		$this->formatNameRepository = $formatNameRepository;
		$this->pokemonNameRepository = $pokemonNameRepository;
		$this->trendPointCalculator = $trendPointCalculator;
	}

	/**
	 * Get the data for a lead usage trend line.
	 *
	 * @param FormatId $formatId
	 * @param int $rating
	 * @param PokemonId $pokemonId
	 * @param LanguageId $languageId
	 *
	 * @return LeadUsageTrendLine
	 */
	public function generate(
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId,
		LanguageId $languageId
	) : LeadUsageTrendLine {
		// Get the usage data.
		$leadsRatedPokemons = $this->leadsRatedPokemonRepository->getByFormatAndRatingAndPokemon(
			$formatId,
			$rating,
			$pokemonId
		);

		// Get the name data.
		$formatName = $this->formatNameRepository->getByLanguageAndFormat($languageId, $formatId);
		$pokemonName = $this->pokemonNameRepository->getByLanguageAndPokemon($languageId, $pokemonId);
	}
}
