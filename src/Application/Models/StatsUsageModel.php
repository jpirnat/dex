<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use Jp\Dex\Domain\Formats\Format;
use Jp\Dex\Domain\Formats\FormatRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Stats\Leads\LeadsRatedPokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\Usage\RatingQueriesInterface;
use Jp\Dex\Domain\Usage\StatsUsagePokemon;
use Jp\Dex\Domain\Usage\StatsUsagePokemonRepositoryInterface;

final class StatsUsageModel
{
	private DateModel $dateModel;
	private FormatRepositoryInterface $formatRepository;
	private RatingQueriesInterface $ratingQueries;
	private LeadsRatedPokemonRepositoryInterface $leadsRatedPokemonRepository;
	private StatsUsagePokemonRepositoryInterface $statsUsagePokemonRepository;


	private string $month;
	private Format $format;
	private int $rating;
	private string $myFormat;
	private string $myRating;
	private LanguageId $languageId;

	/** @var int[] $ratings */
	private array $ratings = [];

	private bool $leadsDataExists;

	/** @var StatsUsagePokemon[] $pokemon */
	private array $pokemon = [];


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
		$this->rating = $rating;
		$this->myFormat = $myFormat;
		$this->myRating = $myRating;
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

		// Get the ratings for this month.
		$this->ratings = $this->ratingQueries->getByMonthAndFormat(
			$thisMonth,
			$this->format->getId()
		);

		// Does leads data exist for this month?
		$this->leadsDataExists = $this->leadsRatedPokemonRepository->hasAny(
			$thisMonth,
			$this->format->getId(),
			$rating
		);

		// Get the Pokémon usage data.
		$this->pokemon = $this->statsUsagePokemonRepository->getByMonth(
			$thisMonth,
			$prevMonth,
			$this->format->getId(),
			$rating,
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
