<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\AbilityUsageMonth;

use Jp\Dex\Application\Models\DateModel;
use Jp\Dex\Domain\Abilities\AbilityDescription;
use Jp\Dex\Domain\Abilities\AbilityDescriptionRepositoryInterface;
use Jp\Dex\Domain\Abilities\AbilityName;
use Jp\Dex\Domain\Abilities\AbilityNameRepositoryInterface;
use Jp\Dex\Domain\Abilities\AbilityRepositoryInterface;
use Jp\Dex\Domain\Formats\FormatRepositoryInterface;
use Jp\Dex\Domain\FormIcons\FormIconRepositoryInterface;
use Jp\Dex\Domain\Forms\FormId;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\PokemonNameRepositoryInterface;
use Jp\Dex\Domain\Pokemon\PokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\Usage\Derived\UsageRatedPokemonAbilityRepositoryInterface;
use Jp\Dex\Domain\Stats\Usage\RatingQueriesInterface;
use Jp\Dex\Domain\Stats\Usage\UsageRatedRepositoryInterface;

class AbilityUsageMonthModel
{
	/** @var DateModel $dateModel */
	private $dateModel;

	/** @var FormatRepositoryInterface $formatRepository */
	private $formatRepository;

	/** @var AbilityRepositoryInterface $abilityRepository */
	private $abilityRepository;

	/** @var UsageRatedRepositoryInterface $usageRatedRepository */
	private $usageRatedRepository;

	/** @var RatingQueriesInterface $ratingQueries */
	private $ratingQueries;

	/** @var AbilityNameRepositoryInterface $abilityNameRepository */
	private $abilityNameRepository;

	/** @var AbilityDescriptionRepositoryInterface $abilityDescriptionRepository */
	private $abilityDescriptionRepository;

	/** @var UsageRatedPokemonAbilityRepositoryInterface $usageRatedPokemonAbilityRepository */
	private $usageRatedPokemonAbilityRepository;

	/** @var PokemonRepositoryInterface $pokemonRepository */
	private $pokemonRepository;

	/** @var PokemonNameRepositoryInterface $pokemonNameRepository */
	private $pokemonNameRepository;

	/** @var FormIconRepositoryInterface $formIconRepository */
	private $formIconRepository;


	/** @var bool $prevMonthDataExists */
	private $prevMonthDataExists;

	/** @var bool $nextMonthDataExists */
	private $nextMonthDataExists;

	/** @var int[] $ratings */
	private $ratings = [];

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

	/** @var AbilityName $abilityName */
	private $abilityName;

	/** @var AbilityDescription $abilityDescription */
	private $abilityDescription;

	/** @var AbilityUsageData[] $abilityUsageDatas */
	private $abilityUsageDatas = [];

	/**
	 * Constructor.
	 *
	 * @param DateModel $dateModel
	 * @param FormatRepositoryInterface $formatRepository
	 * @param AbilityRepositoryInterface $abilityRepository
	 * @param UsageRatedRepositoryInterface $usageRatedRepository
	 * @param RatingQueriesInterface $ratingQueries
	 * @param AbilityNameRepositoryInterface $abilityNameRepository
	 * @param AbilityDescriptionRepositoryInterface $abilityDescriptionRepository
	 * @param UsageRatedPokemonAbilityRepositoryInterface $usageRatedPokemonAbilityRepository
	 * @param PokemonRepositoryInterface $pokemonRepository
	 * @param PokemonNameRepositoryInterface $pokemonNameRepository
	 * @param FormIconRepositoryInterface $formIconRepository
	 */
	public function __construct(
		DateModel $dateModel,
		FormatRepositoryInterface $formatRepository,
		AbilityRepositoryInterface $abilityRepository,
		UsageRatedRepositoryInterface $usageRatedRepository,
		RatingQueriesInterface $ratingQueries,
		AbilityNameRepositoryInterface $abilityNameRepository,
		AbilityDescriptionRepositoryInterface $abilityDescriptionRepository,
		UsageRatedPokemonAbilityRepositoryInterface $usageRatedPokemonAbilityRepository,
		PokemonRepositoryInterface $pokemonRepository,
		PokemonNameRepositoryInterface $pokemonNameRepository,
		FormIconRepositoryInterface $formIconRepository
	) {
		$this->dateModel = $dateModel;
		$this->formatRepository = $formatRepository;
		$this->abilityRepository = $abilityRepository;
		$this->usageRatedRepository = $usageRatedRepository;
		$this->ratingQueries = $ratingQueries;
		$this->abilityNameRepository = $abilityNameRepository;
		$this->abilityDescriptionRepository = $abilityDescriptionRepository;
		$this->usageRatedPokemonAbilityRepository = $usageRatedPokemonAbilityRepository;
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

		// Does usage rated data exist for the previous month?
		$this->prevMonthDataExists = $this->usageRatedRepository->has(
			$prevMonth,
			$format->getId(),
			$rating
		);

		// Does usage rated data exist for the next month?
		$this->nextMonthDataExists = $this->usageRatedRepository->has(
			$nextMonth,
			$format->getId(),
			$rating
		);

		// Get the ratings for this month.
		$this->ratings = $this->ratingQueries->getByMonthAndFormatAndAbility(
			$thisMonth,
			$format->getId(),
			$ability->getId()
		);

		// Get the ability name.
		$this->abilityName = $this->abilityNameRepository->getByLanguageAndAbility(
			$languageId,
			$ability->getId()
		);

		// Get the ability description.
		$this->abilityDescription = $this->abilityDescriptionRepository->getByGenerationAndLanguageAndAbility(
			$format->getGeneration(),
			$languageId,
			$ability->getId()
		);

		// Get usage rated Pokémon ability records for this month.
		$usageRatedPokemonAbilities = $this->usageRatedPokemonAbilityRepository->getByMonthAndFormatAndRatingAndAbility(
			$thisMonth,
			$format->getId(),
			$rating,
			$ability->getId()
		);

		// Get usage rated Pokémon ability records for the previous month.
		$prevMonthPokemonAbilities = $this->usageRatedPokemonAbilityRepository->getByMonthAndFormatAndRatingAndAbility(
			$prevMonth,
			$format->getId(),
			$rating,
			$ability->getId()
		);

		// Get each usage record's data.
		foreach ($usageRatedPokemonAbilities as $usageRatedPokemonAbility) {
			$pokemonId = $usageRatedPokemonAbility->getPokemonId();

			// Get this Pokémon's name.
			$pokemonName = $this->pokemonNameRepository->getByLanguageAndPokemon(
				$languageId,
				$pokemonId
			);

			// Get this Pokémon.
			$pokemon = $this->pokemonRepository->getById($pokemonId);

			// Get this Pokémon's form icon.
			$formIcon = $this->formIconRepository->getByGenerationAndFormAndFemaleAndRight(
				$format->getGeneration(),
				new FormId($pokemonId->value()), // A Pokémon's default form has Pokémon id === form id.
				false,
				false
			);

			// Get this usage rated Pokémon ability's change in usage percent
			// from the previous month.
			$prevMonthUsagePercent = 0;
			if (isset($prevMonthPokemonAbilities[$pokemonId->value()])) {
				$prevMonthUsagePercent = $prevMonthPokemonAbilities[$pokemonId->value()]->getUsagePercent();
			}
			$change = $usageRatedPokemonAbility->getUsagePercent() - $prevMonthUsagePercent;

			$this->abilityUsageDatas[] = new AbilityUsageData(
				$pokemonName->getName(),
				$pokemon->getIdentifier(),
				$formIcon->getImage(),
				$usageRatedPokemonAbility->getPokemonPercent(),
				$usageRatedPokemonAbility->getAbilityPercent(),
				$usageRatedPokemonAbility->getUsagePercent(),
				$change
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
	 * Get the ability usage datas.
	 *
	 * @return AbilityUsageData[]
	 */
	public function getAbilityUsageDatas() : array
	{
		return $this->abilityUsageDatas;
	}
}
