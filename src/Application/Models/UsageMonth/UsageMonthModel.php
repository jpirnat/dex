<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\UsageMonth;

use Jp\Dex\Application\Models\DateModel;
use Jp\Dex\Domain\Formats\FormatRepositoryInterface;
use Jp\Dex\Domain\FormIcons\FormIconRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\PokemonNameRepositoryInterface;
use Jp\Dex\Domain\Pokemon\PokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\Usage\UsagePokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\Usage\UsageRatedPokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\Usage\UsageRatedRepositoryInterface;

class UsageMonthModel
{
	/** @var DateModel $dateModel */
	private $dateModel;

	/** @var FormatRepositoryInterface $formatRepository */
	private $formatRepository;

	/** @var UsagePokemonRepositoryInterface $usagePokemonRepository */
	private $usagePokemonRepository;

	/** @var UsageRatedPokemonRepositoryInterface $usageRatedPokemonRepository */
	private $usageRatedPokemonRepository;

	/** PokemonRepositoryInterface $pokemonRepository */
	private $pokemonRepository;

	/** @var PokemonNameRepositoryInterface $pokemonNameRepository */
	private $pokemonNameRepository;

	/** @var FormIconRepositoryInterface $formIconRepository */
	private $formIconRepository;

	/** @var UsageRatedRepositoryInterface $usageRatedRepository */
	private $usageRatedRepository;

	/** @var int $year */
	private $year;

	/** @var int $month */
	private $month;

	/** @var string $formatIdentifier */
	private $formatIdentifier;

	/** @var int $rating */
	private $rating;

	/** @var UsageData[] $usageDatas */
	private $usageDatas = [];

	/** @var bool $prevMonthDataExists */
	private $prevMonthDataExists;

	/** @var bool $nextMonthDataExists */
	private $nextMonthDataExists;

	/**
	 * Constructor.
	 *
	 * @param DateModel $dateModel
	 * @param FormatRepositoryInterface $formatRepository
	 * @param UsagePokemonRepositoryInterface $usagePokemonRepository
	 * @param UsageRatedPokemonRepositoryInterface $usageRatedPokemonRepository
	 * @param PokemonRepositoryInterface $pokemonRepository
	 * @param PokemonNameRepositoryInterface $pokemonNameRepository
	 * @param FormIconRepositoryInterface $formIconRepository
	 * @param UsageRatedRepositoryInterface $usageRatedRepository
	 */
	public function __construct(
		DateModel $dateModel,
		FormatRepositoryInterface $formatRepository,
		UsagePokemonRepositoryInterface $usagePokemonRepository,
		UsageRatedPokemonRepositoryInterface $usageRatedPokemonRepository,
		PokemonRepositoryInterface $pokemonRepository,
		PokemonNameRepositoryInterface $pokemonNameRepository,
		FormIconRepositoryInterface $formIconRepository,
		UsageRatedRepositoryInterface $usageRatedRepository
	) {
		$this->dateModel = $dateModel;
		$this->formatRepository = $formatRepository;
		$this->usagePokemonRepository = $usagePokemonRepository;
		$this->usageRatedPokemonRepository = $usageRatedPokemonRepository;
		$this->pokemonRepository = $pokemonRepository;
		$this->pokemonNameRepository = $pokemonNameRepository;
		$this->formIconRepository = $formIconRepository;
		$this->usageRatedRepository = $usageRatedRepository;
	}

	/**
	 * Get usage data to recreate a stats usage file, such as
	 * http://www.smogon.com/stats/2014-11/ou-1695.txt.
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

		// Get the previous month and the next month.
		$this->dateModel->setData($year, $month);
		$thisMonth = $this->dateModel->getThisMonth();
		$prevMonth = $this->dateModel->getPrevMonth();
		$nextMonth = $this->dateModel->getNextMonth();

		// Get the format.
		$format = $this->formatRepository->getByIdentifier($formatIdentifier);

		// Get usage Pokémon records for this month.
		$usagePokemons = $this->usagePokemonRepository->getByYearAndMonthAndFormat(
			$thisMonth->getYear(),
			$thisMonth->getMonth(),
			$format->getId()
		);

		// Get usage Pokémon records for the previous month.
		$prevMonthUsages = $this->usagePokemonRepository->getByYearAndMonthAndFormat(
			$prevMonth->getYear(),
			$prevMonth->getMonth(),
			$format->getId()
		);

		// Get usage rated Pokémon records for this month.
		$usageRatedPokemons = $this->usageRatedPokemonRepository->getByYearAndMonthAndFormatAndRating(
			$thisMonth->getYear(),
			$thisMonth->getMonth(),
			$format->getId(),
			$rating
		);

		// Get usage rated Pokémon records for the previous month.
		$prevMonthRateds = $this->usageRatedPokemonRepository->getByYearAndMonthAndFormatAndRating(
			$prevMonth->getYear(),
			$prevMonth->getMonth(),
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

		// Get each usage record's data.
		foreach ($usageRatedPokemons as $usageRatedPokemon) {
			$pokemonId = $usageRatedPokemon->getPokemonId();

			// Get this Pokémon's name.
			$pokemonName = $pokemonNames[$pokemonId->value()];

			// Get this Pokémon.
			$pokemon = $pokemons[$pokemonId->value()];

			// Get this Pokémon's form icon.
			$formIcon = $formIcons[$pokemonId->value()]; // A Pokémon's default form has Pokémon id === form id.

			// Get this Pokémon's non-rated usage record for this month.
			$usagePokemon = $usagePokemons[$pokemonId->value()];

			// Get this Pokémon's change in usage percent from the previous month.
			$prevMonthUsagePercent = 0;
			if (isset($prevMonthRateds[$pokemonId->value()])) {
				$prevMonthUsagePercent = $prevMonthRateds[$pokemonId->value()]->getUsagePercent();
			}
			$usageChange = $usageRatedPokemon->getUsagePercent() - $prevMonthUsagePercent;

			// Get this Pokémon's change in raw percent and real percent from the previous month.
			$prevMonthRawPercent = 0;
			$prevMonthRealPercent = 0;
			if (isset($prevMonthUsages[$pokemonId->value()])) {
				$prevMonthRawPercent = $prevMonthUsages[$pokemonId->value()]->getRawPercent();
				$prevMonthRealPercent = $prevMonthUsages[$pokemonId->value()]->getRealPercent();
			}
			$rawChange = $usagePokemon->getRawPercent() - $prevMonthRawPercent;
			$realChange = $usagePokemon->getRealPercent() - $prevMonthRealPercent;

			$this->usageDatas[] = new UsageData(
				$usageRatedPokemon->getRank(),
				$pokemonName->getName(),
				$pokemon->getIdentifier(),
				$formIcon->getImage(),
				$usageRatedPokemon->getUsagePercent(),
				$usageChange,
				$usagePokemon->getRaw(),
				$usagePokemon->getRawPercent(),
				$rawChange,
				$usagePokemon->getReal(),
				$usagePokemon->getRealPercent(),
				$realChange
			);
		}

		// Does usage rated data exist for the previous month?
		$this->prevMonthDataExists = $this->usageRatedRepository->has(
			$prevMonth->getYear(),
			$prevMonth->getMonth(),
			$format->getId(),
			$rating
		);

		// Does usage rated data exist for the next month?
		$this->nextMonthDataExists = $this->usageRatedRepository->has(
			$nextMonth->getYear(),
			$nextMonth->getMonth(),
			$format->getId(),
			$rating
		);
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
	 * Get the usage datas.
	 *
	 * @return UsageData[]
	 */
	public function getUsageDatas() : array
	{
		return $this->usageDatas;
	}

	/**
	 * Does usage rated data exist for the previous month?
	 *
	 * @return bool
	 */
	public function doesPrevMonthDataExist() : bool
	{
		return $this->prevMonthDataExists;
	}

	/**
	 * Does usage rated data exist for the next month?
	 *
	 * @return bool
	 */
	public function doesNextMonthDataExist() : bool
	{
		return $this->nextMonthDataExists;
	}
}
