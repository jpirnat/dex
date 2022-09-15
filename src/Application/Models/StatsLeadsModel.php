<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use DateTime;
use Jp\Dex\Domain\Formats\Format;
use Jp\Dex\Domain\Formats\FormatRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Leads\StatsLeadsPokemon;
use Jp\Dex\Domain\Leads\StatsLeadsPokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\Usage\RatingQueriesInterface;
use Jp\Dex\Domain\Stats\Usage\UsageRatedQueriesInterface;

final class StatsLeadsModel
{
	private string $month;
	private Format $format;
	private int $rating;
	private LanguageId $languageId;

	/** @var int[] $ratings */
	private array $ratings = [];

	/** @var StatsLeadsPokemon[] $pokemon */
	private array $pokemon = [];

	/** @var DateTime[] $months */
	private array $months = [];


	public function __construct(
		private DateModel $dateModel,
		private FormatRepositoryInterface $formatRepository,
		private RatingQueriesInterface $ratingQueries,
		private StatsLeadsPokemonRepositoryInterface $statsLeadsPokemonRepository,
		private UsageRatedQueriesInterface $usageRatedQueries,
	) {}


	/**
	 * Get leads data to recreate a stats leads file, such as
	 * http://www.smogon.com/stats/leads/2014-11/ou-1695.txt.
	 */
	public function setData(
		string $month,
		string $formatIdentifier,
		int $rating,
		LanguageId $languageId
	) : void {
		$this->month = $month;
		$this->rating = $rating;
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

		// Get the Pokémon usage data.
		$this->pokemon = $this->statsLeadsPokemonRepository->getByMonth(
			$thisMonth,
			$prevMonth,
			$this->format->getId(),
			$rating,
			$this->format->getGenerationId(),
			$languageId
		);

		$this->months = $this->usageRatedQueries->getMonthsWithData(
			$this->format->getId(),
			$rating,
		);
	}


	/**
	 * Get the month.
	 */
	public function getMonth() : string
	{
		return $this->month;
	}

	/**
	 * Get the format.
	 */
	public function getFormat() : Format
	{
		return $this->format;
	}

	/**
	 * Get the rating.
	 */
	public function getRating() : int
	{
		return $this->rating;
	}

	/**
	 * Get the language id.
	 */
	public function getLanguageId() : LanguageId
	{
		return $this->languageId;
	}

	/**
	 * Get the date model.
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
	 * Get the Pokémon.
	 *
	 * @return StatsLeadsPokemon[]
	 */
	public function getPokemon() : array
	{
		return $this->pokemon;
	}

	/**
	 * Get the months.
	 *
	 * @return DateTime[]
	 */
	public function getMonths() : array
	{
		return $this->months;
	}
}
