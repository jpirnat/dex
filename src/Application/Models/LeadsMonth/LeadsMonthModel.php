<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\LeadsMonth;

use Jp\Dex\Application\Models\DateHelper;
use Jp\Dex\Domain\Formats\FormatRepositoryInterface;
use Jp\Dex\Domain\FormIcons\FormIconRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\PokemonNameRepositoryInterface;
use Jp\Dex\Domain\Pokemon\PokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\Leads\LeadsPokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\Leads\LeadsRatedPokemonRepositoryInterface;
use Jp\Dex\Domain\YearMonth;

class LeadsMonthModel
{
	/** @var FormatRepositoryInterface $formatRepository */
	private $formatRepository;

	/** @var DateHelper $dateHelper */
	private $dateHelper;

	/** @var LeadsPokemonRepositoryInterface $leadsPokemonRepository */
	private $leadsPokemonRepository;

	/** @var LeadsRatedPokemonRepositoryInterface $leadsRatedPokemonRepository */
	private $leadsRatedPokemonRepository;

	/** PokemonRepositoryInterface $pokemonRepository */
	private $pokemonRepository;

	/** @var PokemonNameRepositoryInterface $pokemonNameRepository */
	private $pokemonNameRepository;

	/** @var FormIconRepositoryInterface $formIconRepository */
	private $formIconRepository;

	/** @var int $year */
	private $year;

	/** @var int $month */
	private $month;

	/** @var string $formatIdentifier */
	private $formatIdentifier;

	/** @var int $rating */
	private $rating;

	/** @var LeadsData[] $leadsDatas */
	private $leadsDatas = [];

	/**
	 * Constructor.
	 *
	 * @param FormatRepositoryInterface $formatRepository
	 * @param DateHelper $dateHelper
	 * @param LeadsPokemonRepositoryInterface $leadsPokemonRepository
	 * @param LeadsRatedPokemonRepositoryInterface $leadsRatedPokemonRepository
	 * @param PokemonRepositoryInterface $pokemonRepository
	 * @param PokemonNameRepositoryInterface $pokemonNameRepository
	 * @param FormIconRepositoryInterface $formIconRepository
	 */
	public function __construct(
		FormatRepositoryInterface $formatRepository,
		DateHelper $dateHelper,
		LeadsPokemonRepositoryInterface $leadsPokemonRepository,
		LeadsRatedPokemonRepositoryInterface $leadsRatedPokemonRepository,
		PokemonRepositoryInterface $pokemonRepository,
		PokemonNameRepositoryInterface $pokemonNameRepository,
		FormIconRepositoryInterface $formIconRepository
	) {
		$this->formatRepository = $formatRepository;
		$this->dateHelper = $dateHelper;
		$this->leadsPokemonRepository = $leadsPokemonRepository;
		$this->leadsRatedPokemonRepository = $leadsRatedPokemonRepository;
		$this->pokemonRepository = $pokemonRepository;
		$this->pokemonNameRepository = $pokemonNameRepository;
		$this->formIconRepository = $formIconRepository;
	}

	/**
	 * Get leads data to recreate a stats leads file, such as
	 * http://www.smogon.com/stats/leads/2014-11/ou-1695.txt.
	 *
	 * @param int $year
	 * @param int $month
	 * @param string $formatIdentifier
	 * @param int $rating
	 * @param LanguageId $languageId
	 *
	 * @return void
	 */
	public function setData(
		int $year,
		int $month,
		string $formatIdentifier,
		int $rating,
		LanguageId $languageId
	) : void {
		$this->year = $year;
		$this->month = $month;
		$this->formatIdentifier = $formatIdentifier;
		$this->rating = $rating;

		// Get the format.
		$format = $this->formatRepository->getByIdentifier($formatIdentifier);

		// Calculate the previous month.
		$thisMonth = new YearMonth($year, $month);
		$lastMonth = $this->dateHelper->getPreviousMonth($thisMonth);

		// Get leads Pokémon records for this month.
		$leadsPokemons = $this->leadsPokemonRepository->getByYearAndMonthAndFormat(
			$thisMonth->getYear(),
			$thisMonth->getMonth(),
			$format->getId()
		);

		// Get leads Pokémon records for last month.
		$lastMonthLeads = $this->leadsPokemonRepository->getByYearAndMonthAndFormat(
			$lastMonth->getYear(),
			$lastMonth->getMonth(),
			$format->getId()
		);

		// Get leads rated Pokémon records for this month.
		$leadsRatedPokemons = $this->leadsRatedPokemonRepository->getByYearAndMonthAndFormatAndRating(
			$thisMonth->getYear(),
			$thisMonth->getMonth(),
			$format->getId(),
			$rating
		);

		// Get leads rated Pokémon records for last month.
		$lastMonthRateds = $this->leadsRatedPokemonRepository->getByYearAndMonthAndFormatAndRating(
			$lastMonth->getYear(),
			$lastMonth->getMonth(),
			$format->getId(),
			$rating
		);

		// Get Pokémon.
		$pokemons = $this->pokemonRepository->getAll();

		// Get Pokémon names.
		$pokemonNames = $this->pokemonNameRepository->getByLanguage($languageId);

		// Get form icons.
		$formIcons = $this->formIconRepository->getByGenerationAndFemaleAndRight(
			$format->getGeneration(),
			false,
			false
		);

		// Get each leads record's data.
		foreach ($leadsRatedPokemons as $leadsRatedPokemon) {
			$pokemonId = $leadsRatedPokemon->getPokemonId();

			// Get this Pokémon's name.
			$pokemonName = $pokemonNames[$pokemonId->value()];

			// Get this Pokémon.
			$pokemon = $pokemons[$pokemonId->value()];

			// Get this Pokémon's form icon.
			$formIcon = $formIcons[$pokemonId->value()]; // A Pokémon's default form has Pokémon id === form id.

			// Get this Pokémon's non-rated usage record for this month.
			$leadsPokemon = $leadsPokemons[$pokemonId->value()];

			// Get this Pokémon's change in usage percent since last month.
			$lastMonthUsagePercent = 0;
			if (isset($lastMonthRateds[$pokemonId->value()])) {
				$lastMonthUsagePercent = $lastMonthRateds[$pokemonId->value()]->getUsagePercent();
			}
			$usageChange = $leadsRatedPokemon->getUsagePercent() - $lastMonthUsagePercent;

			// Get this Pokémon's change in raw percent since last month.
			$lastMonthRawPercent = 0;
			if (isset($lastMonthLeads[$pokemonId->value()])) {
				$lastMonthRawPercent = $lastMonthLeads[$pokemonId->value()]->getRawPercent();
			}
			$rawChange = $leadsPokemon->getRawPercent() - $lastMonthRawPercent;

			$this->leadsDatas[] = new LeadsData(
				$leadsRatedPokemon->getRank(),
				$pokemonName->getName(),
				$pokemon->getIdentifier(),
				$formIcon->getImage(),
				$leadsRatedPokemon->getUsagePercent(),
				$usageChange,
				$leadsPokemon->getRaw(),
				$leadsPokemon->getRawPercent(),
				$rawChange
			);
		}
	}

	/**
	 * Get the year.
	 *
	 * @return int
	 */
	public function getYear() : int
	{
		return $this->year;
	}

	/**
	 * Get the month.
	 *
	 * @return int
	 */
	public function getMonth() : int
	{
		return $this->month;
	}

	/**
	 * Get the format identifier.
	 *
	 * @return string
	 */
	public function getFormatIdentifier() : string
	{
		return $this->formatIdentifier;
	}

	/**
	 * Get the rating.
	 *
	 * @return int
	 */
	public function getRating() : int
	{
		return $this->rating;
	}

	/**
	 * Get the leads datas.
	 *
	 * @return LeadsData[]
	 */
	public function getLeadsDatas() : array
	{
		return $this->leadsDatas;
	}
}
