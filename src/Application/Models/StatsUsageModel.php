<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use DateTime;
use Jp\Dex\Domain\Formats\Format;
use Jp\Dex\Domain\Formats\FormatRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Stats\Leads\LeadsRatedPokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\StatId;
use Jp\Dex\Domain\Stats\StatNameRepositoryInterface;
use Jp\Dex\Domain\Stats\Usage\RatingQueriesInterface;
use Jp\Dex\Domain\Stats\Usage\UsageRatedQueriesInterface;
use Jp\Dex\Domain\Usage\StatsUsagePokemon;
use Jp\Dex\Domain\Usage\StatsUsagePokemonRepositoryInterface;

final class StatsUsageModel
{
	private string $month;
	private Format $format;
	private int $rating;
	private string $myFormat;
	private string $myRating;
	private LanguageId $languageId;

	/** @var int[] $ratings */
	private array $ratings = [];

	private bool $leadsDataExists = false;
	private string $speedName = '';

	/** @var StatsUsagePokemon[] $pokemon */
	private array $pokemon = [];

	/** @var DateTime[] $months */
	private array $months = [];


	public function __construct(
		private readonly DateModel $dateModel,
		private readonly FormatRepositoryInterface $formatRepository,
		private readonly RatingQueriesInterface $ratingQueries,
		private readonly LeadsRatedPokemonRepositoryInterface $leadsRatedPokemonRepository,
		private readonly StatNameRepositoryInterface $statNameRepository,
		private readonly StatsUsagePokemonRepositoryInterface $statsUsagePokemonRepository,
		private readonly UsageRatedQueriesInterface $usageRatedQueries,
	) {}


	/**
	 * Get usage data to recreate a stats usage file, such as
	 * http://www.smogon.com/stats/2014-11/ou-1695.txt.
	 */
	public function setData(
		string $month,
		string $formatIdentifier,
		int $rating,
		string $myFormat,
		string $myRating,
		LanguageId $languageId,
	) : void {
		$this->month = $month;
		$this->rating = $rating;
		$this->myFormat = $myFormat;
		$this->myRating = $myRating;
		$this->languageId = $languageId;

		// Get the format.
		$this->format = $this->formatRepository->getByIdentifier(
			$formatIdentifier,
			$languageId,
		);

		// Get the previous month and the next month.
		$this->dateModel->setMonthAndFormat($month, $this->format->getId());
		$thisMonth = $this->dateModel->getThisMonth();
		$prevMonth = $this->dateModel->getPrevMonth();

		// Get the ratings for this month.
		$this->ratings = $this->ratingQueries->getByMonthAndFormat(
			$thisMonth,
			$this->format->getId(),
		);

		// Does leads data exist for this month?
		$this->leadsDataExists = $this->leadsRatedPokemonRepository->hasAny(
			$thisMonth,
			$this->format->getId(),
			$rating,
		);

		$speedName = $this->statNameRepository->getByLanguageAndStat(
			$languageId,
			new StatId(StatId::SPEED),
		);
		$this->speedName = $speedName->getName();

		// Get the Pokémon usage data.
		$this->pokemon = $this->statsUsagePokemonRepository->getByMonth(
			$thisMonth,
			$prevMonth,
			$this->format->getId(),
			$rating,
			$languageId,
		);

		$this->months = $this->usageRatedQueries->getMonthsWithData(
			$this->format->getId(),
			$rating,
		);
	}


	public function getMonth() : string
	{
		return $this->month;
	}

	public function getFormat() : Format
	{
		return $this->format;
	}

	public function getRating() : int
	{
		return $this->rating;
	}

	public function getMyFormat() : string
	{
		return $this->myFormat;
	}

	public function getMyRating() : string
	{
		return $this->myRating;
	}

	public function getLanguageId() : LanguageId
	{
		return $this->languageId;
	}

	public function getDateModel() : DateModel
	{
		return $this->dateModel;
	}

	/**
	 * @return int[]
	 */
	public function getRatings() : array
	{
		return $this->ratings;
	}

	public function doesLeadsDataExist() : bool
	{
		return $this->leadsDataExists;
	}

	public function getSpeedName() : string
	{
		return $this->speedName;
	}

	/**
	 * @return StatsUsagePokemon[]
	 */
	public function getPokemon() : array
	{
		return $this->pokemon;
	}

	/**
	 * @return DateTime[]
	 */
	public function getMonths() : array
	{
		return $this->months;
	}
}
