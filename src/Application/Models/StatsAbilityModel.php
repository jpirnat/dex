<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use Jp\Dex\Domain\Abilities\AbilityDescription;
use Jp\Dex\Domain\Abilities\AbilityDescriptionRepositoryInterface;
use Jp\Dex\Domain\Abilities\AbilityName;
use Jp\Dex\Domain\Abilities\AbilityNameRepositoryInterface;
use Jp\Dex\Domain\Abilities\AbilityRepositoryInterface;
use Jp\Dex\Domain\Formats\Format;
use Jp\Dex\Domain\Formats\FormatRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Stats\Usage\RatingQueriesInterface;
use Jp\Dex\Domain\Usage\StatsAbilityPokemon;
use Jp\Dex\Domain\Usage\StatsAbilityPokemonRepositoryInterface;

final class StatsAbilityModel
{
	/** @var DateModel $dateModel */
	private $dateModel;

	/** @var FormatRepositoryInterface $formatRepository */
	private $formatRepository;

	/** @var AbilityRepositoryInterface $abilityRepository */
	private $abilityRepository;

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

	/** @var Format $format */
	private $format;

	/** @var int $rating */
	private $rating;

	/** @var string $abilityIdentifier */
	private $abilityIdentifier;

	/** @var LanguageId $languageId */
	private $languageId;

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
	 * @param RatingQueriesInterface $ratingQueries
	 * @param AbilityNameRepositoryInterface $abilityNameRepository
	 * @param AbilityDescriptionRepositoryInterface $abilityDescriptionRepository
	 * @param StatsAbilityPokemonRepositoryInterface $statsAbilityPokemonRepository
	 */
	public function __construct(
		DateModel $dateModel,
		FormatRepositoryInterface $formatRepository,
		AbilityRepositoryInterface $abilityRepository,
		RatingQueriesInterface $ratingQueries,
		AbilityNameRepositoryInterface $abilityNameRepository,
		AbilityDescriptionRepositoryInterface $abilityDescriptionRepository,
		StatsAbilityPokemonRepositoryInterface $statsAbilityPokemonRepository
	) {
		$this->dateModel = $dateModel;
		$this->formatRepository = $formatRepository;
		$this->abilityRepository = $abilityRepository;
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
		$this->rating = $rating;
		$this->abilityIdentifier = $abilityIdentifier;
		$this->languageId = $languageId;

		// Get the format.
		$this->format = $this->formatRepository->getByIdentifier(
			$formatIdentifier,
			$languageId
		);

		// Get the previous month and the next month.
		$this->dateModel->setMonthAndFormat($month, $this->format->getId());
		$thisMonth = $this->dateModel->getThisMonth();
		$prevMonth = $this->dateModel->getPrevMonth();

		// Get the ability.
		$ability = $this->abilityRepository->getByIdentifier($abilityIdentifier);

		// Get the ratings for this month.
		$this->ratings = $this->ratingQueries->getByMonthAndFormat(
			$thisMonth,
			$this->format->getId()
		);

		// Get the ability name.
		$this->abilityName = $this->abilityNameRepository->getByLanguageAndAbility(
			$languageId,
			$ability->getId()
		);

		// Get the ability description.
		$this->abilityDescription = $this->abilityDescriptionRepository->getByGenerationAndLanguageAndAbility(
			$this->format->getGenerationId(),
			$languageId,
			$ability->getId()
		);

		// Get the Pokémon usage data.
		$this->pokemon = $this->statsAbilityPokemonRepository->getByMonth(
			$thisMonth,
			$prevMonth,
			$this->format->getId(),
			$rating,
			$ability->getId(),
			$this->format->getGenerationId(),
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
	 * Get the format.
	 *
	 * @return Format
	 */
	public function getFormat() : Format
	{
		return $this->format;
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
