<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\StatsUsage;

use Jp\Dex\Application\Models\DateModel;
use Jp\Dex\Domain\Formats\FormatRepositoryInterface;
use Jp\Dex\Domain\FormIcons\FormIconRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\PokemonNameRepositoryInterface;
use Jp\Dex\Domain\Pokemon\PokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\Leads\LeadsRatedPokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\Usage\MonthQueriesInterface;
use Jp\Dex\Domain\Stats\Usage\RatingQueriesInterface;
use Jp\Dex\Domain\Stats\Usage\UsagePokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\Usage\UsageRatedPokemonRepositoryInterface;

class StatsUsageModel
{
	/** @var DateModel $dateModel */
	private $dateModel;

	/** @var FormatRepositoryInterface $formatRepository */
	private $formatRepository;

	/** @var MonthQueriesInterface $monthQueries */
	private $monthQueries;

	/** @var RatingQueriesInterface $ratingQueries */
	private $ratingQueries;

	/** @var UsagePokemonRepositoryInterface $usagePokemonRepository */
	private $usagePokemonRepository;

	/** @var UsageRatedPokemonRepositoryInterface $usageRatedPokemonRepository */
	private $usageRatedPokemonRepository;

	/** @var LeadsRatedPokemonRepositoryInterface $leadsRatedPokemonRepository */
	private $leadsRatedPokemonRepository;

	/** @var PokemonRepositoryInterface $pokemonRepository */
	private $pokemonRepository;

	/** @var PokemonNameRepositoryInterface $pokemonNameRepository */
	private $pokemonNameRepository;

	/** @var FormIconRepositoryInterface $formIconRepository */
	private $formIconRepository;


	/** @var string $month */
	private $month;

	/** @var string $formatIdentifier */
	private $formatIdentifier;

	/** @var int $rating */
	private $rating;

	/** @var string $myFormat */
	private $myFormat;

	/** @var string $myRating */
	private $myRating;

	/** @var LanguageId $languageId */
	private $languageId;

	/** @var bool $prevMonthDataExists */
	private $prevMonthDataExists;

	/** @var bool $nextMonthDataExists */
	private $nextMonthDataExists;

	/** @var int[] $ratings */
	private $ratings = [];

	/** @var bool $leadsDataExists */
	private $leadsDataExists;

	/** @var UsageData[] $usageDatas */
	private $usageDatas = [];


	/**
	 * Constructor.
	 *
	 * @param DateModel $dateModel
	 * @param FormatRepositoryInterface $formatRepository
	 * @param MonthQueriesInterface $monthQueries
	 * @param RatingQueriesInterface $ratingQueries
	 * @param UsagePokemonRepositoryInterface $usagePokemonRepository
	 * @param UsageRatedPokemonRepositoryInterface $usageRatedPokemonRepository
	 * @param LeadsRatedPokemonRepositoryInterface $leadsRatedPokemonRepository
	 * @param PokemonRepositoryInterface $pokemonRepository
	 * @param PokemonNameRepositoryInterface $pokemonNameRepository
	 * @param FormIconRepositoryInterface $formIconRepository
	 */
	public function __construct(
		DateModel $dateModel,
		FormatRepositoryInterface $formatRepository,
		MonthQueriesInterface $monthQueries,
		RatingQueriesInterface $ratingQueries,
		UsagePokemonRepositoryInterface $usagePokemonRepository,
		UsageRatedPokemonRepositoryInterface $usageRatedPokemonRepository,
		LeadsRatedPokemonRepositoryInterface $leadsRatedPokemonRepository,
		PokemonRepositoryInterface $pokemonRepository,
		PokemonNameRepositoryInterface $pokemonNameRepository,
		FormIconRepositoryInterface $formIconRepository
	) {
		$this->dateModel = $dateModel;
		$this->formatRepository = $formatRepository;
		$this->monthQueries = $monthQueries;
		$this->ratingQueries = $ratingQueries;
		$this->usagePokemonRepository = $usagePokemonRepository;
		$this->usageRatedPokemonRepository = $usageRatedPokemonRepository;
		$this->leadsRatedPokemonRepository = $leadsRatedPokemonRepository;
		$this->pokemonRepository = $pokemonRepository;
		$this->pokemonNameRepository = $pokemonNameRepository;
		$this->formIconRepository = $formIconRepository;
	}

	/**
	 * Get usage data to recreate a stats usage file, such as
	 * http://www.smogon.com/stats/2014-11/ou-1695.txt.
	 *
	 * @param string $month
	 * @param string $formatIdentifier
	 * @param int $rating
	 * @param string $myFormat
	 * @param string $myRating
	 * @param LanguageId $languageId
	 *
	 * @return void
	 */
	public function setData(
		string $month,
		string $formatIdentifier,
		int $rating,
		string $myFormat,
		string $myRating,
		LanguageId $languageId
	) : void {
		$this->month = $month;
		$this->formatIdentifier = $formatIdentifier;
		$this->rating = $rating;
		$this->myFormat = $myFormat;
		$this->myRating = $myRating;
		$this->languageId = $languageId;

		// Get the previous month and the next month.
		$this->dateModel->setData($month);
		$thisMonth = $this->dateModel->getThisMonth();
		$prevMonth = $this->dateModel->getPrevMonth();
		$nextMonth = $this->dateModel->getNextMonth();

		// Get the format.
		$format = $this->formatRepository->getByIdentifier($formatIdentifier);

		// Does usage data exist for the previous month?
		$this->prevMonthDataExists = $this->monthQueries->doesMonthFormatDataExist(
			$prevMonth,
			$format->getId()
		);

		// Does usage data exist for the next month?
		$this->nextMonthDataExists = $this->monthQueries->doesMonthFormatDataExist(
			$nextMonth,
			$format->getId()
		);

		// Get the ratings for this month.
		$this->ratings = $this->ratingQueries->getByMonthAndFormat(
			$thisMonth,
			$format->getId()
		);

		// Does leads rated data exist for this month?
		$this->leadsDataExists = $this->leadsRatedPokemonRepository->hasAny(
			$thisMonth,
			$format->getId(),
			$rating
		);

		// Get usage Pokémon records for this month.
		$usagePokemons = $this->usagePokemonRepository->getByMonthAndFormat(
			$thisMonth,
			$format->getId()
		);

		// Get usage Pokémon records for the previous month.
		$prevMonthUsages = $this->usagePokemonRepository->getByMonthAndFormat(
			$prevMonth,
			$format->getId()
		);

		// Get usage rated Pokémon records for this month.
		$usageRatedPokemons = $this->usageRatedPokemonRepository->getByMonthAndFormatAndRating(
			$thisMonth,
			$format->getId(),
			$rating
		);

		// Get usage rated Pokémon records for the previous month.
		$prevMonthRateds = $this->usageRatedPokemonRepository->getByMonthAndFormatAndRating(
			$prevMonth,
			$format->getId(),
			$rating
		);

		// Get Pokémon.
		$pokemons = $this->pokemonRepository->getAll();

		// Get Pokémon names.
		$pokemonNames = $this->pokemonNameRepository->getByLanguage($languageId);

		// Get form icons.
		$formIcons = $this->formIconRepository->getByGenerationAndFemaleAndRight(
			$format->getGenerationId(),
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
	}

	/**
	 * Get the month.
	 *
	 * @return string
	 */
	public function getMonth() : string
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
	 * Get the user's default format identifier.
	 *
	 * @return string
	 */
	public function getMyFormat() : string
	{
		return $this->myFormat;
	}

	/**
	 * Get the user's default rating.
	 *
	 * @return string
	 */
	public function getMyRating() : string
	{
		return $this->myRating;
	}

	/**
	 * Get the language id.
	 *
	 * @return LanguageId
	 */
	public function getLanguageId() : LanguageId
	{
		return $this->languageId;
	}

	/**
	 * Get the date model.
	 *
	 * @return DateModel
	 */
	public function getDateModel() : DateModel
	{
		return $this->dateModel;
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

	/**
	 * Get the ratings for this month.
	 *
	 * @return int[]
	 */
	public function getRatings() : array
	{
		return $this->ratings;
	}

	/**
	 * Does leads rated data exist for this month?
	 *
	 * @return bool
	 */
	public function doesLeadsDataExist() : bool
	{
		return $this->leadsDataExists;
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
}
