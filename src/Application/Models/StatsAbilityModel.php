<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use Jp\Dex\Domain\Abilities\AbilityDescription;
use Jp\Dex\Domain\Abilities\AbilityDescriptionRepositoryInterface;
use Jp\Dex\Domain\Abilities\AbilityName;
use Jp\Dex\Domain\Abilities\AbilityNameRepositoryInterface;
use Jp\Dex\Domain\Abilities\AbilityRepositoryInterface;
use Jp\Dex\Domain\Formats\FormatRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Stats\Usage\MonthQueriesInterface;
use Jp\Dex\Domain\Stats\Usage\RatingQueriesInterface;
use Jp\Dex\Domain\Usage\StatsAbilityPokemon;
use Jp\Dex\Domain\Usage\StatsAbilityPokemonRepositoryInterface;

class StatsAbilityModel
{
	/** @var DateModel $dateModel */
	private $dateModel;

	/** @var FormatRepositoryInterface $formatRepository */
	private $formatRepository;

	/** @var AbilityRepositoryInterface $abilityRepository */
	private $abilityRepository;

	/** @var MonthQueriesInterface $monthQueries */
	private $monthQueries;

	/** @var RatingQueriesInterface $ratingQueries */
	private $ratingQueries;

	/** @var AbilityNameRepositoryInterface $abilityNameRepository */
	private $abilityNameRepository;

	/** @var AbilityDescriptionRepositoryInterface $abilityDescriptionRepository */
	private $abilityDescriptionRepository;

	/** @var StatsAbilityPokemonRepositoryInterface $statsAbilityPokemonRepository */
	private $statsAbilityPokemonRepository;


	/** @var string $month */
	private $month;

	/** @var string $formatIdentifier */
	private $formatIdentifier;

	/** @var int $rating */
	private $rating;

	/** @var string $abilityIdentifier */
	private $abilityIdentifier;

	/** @var LanguageId $languageId */
	private $languageId;

	/** @var bool $prevMonthDataExists */
	private $prevMonthDataExists;

	/** @var bool $nextMonthDataExists */
	private $nextMonthDataExists;

	/** @var int[] $ratings */
	private $ratings = [];

	/** @var AbilityName $abilityName */
	private $abilityName;

	/** @var AbilityDescription $abilityDescription */
	private $abilityDescription;

	/** @var StatsAbilityPokemon[] $pokemon */
	private $pokemon = [];


	/**
	 * Constructor.
	 *
	 * @param DateModel $dateModel
	 * @param FormatRepositoryInterface $formatRepository
	 * @param AbilityRepositoryInterface $abilityRepository
	 * @param MonthQueriesInterface $monthQueries
	 * @param RatingQueriesInterface $ratingQueries
	 * @param AbilityNameRepositoryInterface $abilityNameRepository
	 * @param AbilityDescriptionRepositoryInterface $abilityDescriptionRepository
	 * @param StatsAbilityPokemonRepositoryInterface $statsAbilityPokemonRepository
	 */
	public function __construct(
		DateModel $dateModel,
		FormatRepositoryInterface $formatRepository,
		AbilityRepositoryInterface $abilityRepository,
		MonthQueriesInterface $monthQueries,
		RatingQueriesInterface $ratingQueries,
		AbilityNameRepositoryInterface $abilityNameRepository,
		AbilityDescriptionRepositoryInterface $abilityDescriptionRepository,
		StatsAbilityPokemonRepositoryInterface $statsAbilityPokemonRepository
	) {
		$this->dateModel = $dateModel;
		$this->formatRepository = $formatRepository;
		$this->abilityRepository = $abilityRepository;
		$this->monthQueries = $monthQueries;
		$this->ratingQueries = $ratingQueries;
		$this->abilityNameRepository = $abilityNameRepository;
		$this->abilityDescriptionRepository = $abilityDescriptionRepository;
		$this->statsAbilityPokemonRepository = $statsAbilityPokemonRepository;
	}

	/**
	 * Get usage data to recreate a stats usage file, such as
	 * http://www.smogon.com/stats/2014-11/ou-1695.txt.
	 *
	 * @param string $month
	 * @param string $formatIdentifier
	 * @param int $rating
	 * @param string $abilityIdentifier
	 * @param LanguageId $languageId
	 *
	 * @return void
	 */
	public function setData(
		string $month,
		string $formatIdentifier,
		int $rating,
		string $abilityIdentifier,
		LanguageId $languageId
	) : void {
		$this->month = $month;
		$this->formatIdentifier = $formatIdentifier;
		$this->rating = $rating;
		$this->abilityIdentifier = $abilityIdentifier;
		$this->languageId = $languageId;

		// Get the previous month and the next month.
		$this->dateModel->setData($month);
		$thisMonth = $this->dateModel->getThisMonth();
		$prevMonth = $this->dateModel->getPrevMonth();
		$nextMonth = $this->dateModel->getNextMonth();

		// Get the format.
		$format = $this->formatRepository->getByIdentifier($formatIdentifier);

		// Get the ability.
		$ability = $this->abilityRepository->getByIdentifier($abilityIdentifier);

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

		// Get the ability name.
		$this->abilityName = $this->abilityNameRepository->getByLanguageAndAbility(
			$languageId,
			$ability->getId()
		);

		// Get the ability description.
		$this->abilityDescription = $this->abilityDescriptionRepository->getByGenerationAndLanguageAndAbility(
			$format->getGenerationId(),
			$languageId,
			$ability->getId()
		);

		// Get the Pokémon usage data.
		$this->pokemon = $this->statsAbilityPokemonRepository->getByMonth(
			$thisMonth,
			$prevMonth,
			$format->getId(),
			$rating,
			$ability->getId(),
			$format->getGenerationId(),
			$languageId
		);
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
	 * Get the ability identifier.
	 *
	 * @return string
	 */
	public function getAbilityIdentifier() : string
	{
		return $this->abilityIdentifier;
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
	 * Get the ability name.
	 *
	 * @return AbilityName
	 */
	public function getAbilityName() : AbilityName
	{
		return $this->abilityName;
	}

	/**
	 * Get the ability description.
	 *
	 * @return AbilityDescription
	 */
	public function getAbilityDescription() : AbilityDescription
	{
		return $this->abilityDescription;
	}

	/**
	 * Get the Pokémon.
	 *
	 * @return StatsAbilityPokemon[]
	 */
	public function getPokemon() : array
	{
		return $this->pokemon;
	}
}
