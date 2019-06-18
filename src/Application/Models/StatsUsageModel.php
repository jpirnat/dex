<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use Jp\Dex\Domain\Formats\FormatRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Stats\Leads\LeadsRatedPokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\Usage\RatingQueriesInterface;
use Jp\Dex\Domain\Usage\StatsUsagePokemon;
use Jp\Dex\Domain\Usage\StatsUsagePokemonRepositoryInterface;

class StatsUsageModel
{
	/** @var DateModel $dateModel */
	private $dateModel;

	/** @var FormatRepositoryInterface $formatRepository */
	private $formatRepository;

	/** @var RatingQueriesInterface $ratingQueries */
	private $ratingQueries;

	/** @var LeadsRatedPokemonRepositoryInterface $leadsRatedPokemonRepository */
	private $leadsRatedPokemonRepository;

	/** @var StatsUsagePokemonRepositoryInterface $statsUsagePokemonRepository */
	private $statsUsagePokemonRepository;


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

	/** @var int[] $ratings */
	private $ratings = [];

	/** @var bool $leadsDataExists */
	private $leadsDataExists;

	/** @var StatsUsagePokemon[] $pokemon */
	private $pokemon = [];


	/**
	 * Constructor.
	 *
	 * @param DateModel $dateModel
	 * @param FormatRepositoryInterface $formatRepository
	 * @param RatingQueriesInterface $ratingQueries
	 * @param LeadsRatedPokemonRepositoryInterface $leadsRatedPokemonRepository
	 * @param StatsUsagePokemonRepositoryInterface $statsUsagePokemonRepository
	 */
	public function __construct(
		DateModel $dateModel,
		FormatRepositoryInterface $formatRepository,
		RatingQueriesInterface $ratingQueries,
		LeadsRatedPokemonRepositoryInterface $leadsRatedPokemonRepository,
		StatsUsagePokemonRepositoryInterface $statsUsagePokemonRepository
	) {
		$this->dateModel = $dateModel;
		$this->formatRepository = $formatRepository;
		$this->ratingQueries = $ratingQueries;
		$this->leadsRatedPokemonRepository = $leadsRatedPokemonRepository;
		$this->statsUsagePokemonRepository = $statsUsagePokemonRepository;
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

		// Get the format.
		$format = $this->formatRepository->getByIdentifier($formatIdentifier);

		// Get the previous month and the next month.
		$this->dateModel->setMonthAndFormat($month, $format->getId());
		$thisMonth = $this->dateModel->getThisMonth();
		$prevMonth = $this->dateModel->getPrevMonth();

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

		// Get the Pokémon usage data.
		$this->pokemon = $this->statsUsagePokemonRepository->getByMonth(
			$thisMonth,
			$prevMonth,
			$format->getId(),
			$rating,
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
	 * Get the Pokémon.
	 *
	 * @return StatsUsagePokemon[]
	 */
	public function getPokemon() : array
	{
		return $this->pokemon;
	}
}
