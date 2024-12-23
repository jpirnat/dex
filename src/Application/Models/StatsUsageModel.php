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
	private(set) string $month;
	private(set) Format $format;
	private(set) int $rating;
	private(set) string $myFormat;
	private(set) string $myRating;
	private(set) LanguageId $languageId;

	/** @var int[] $ratings */
	private(set) array $ratings = [];

	private(set) bool $showLeadsLink = false;
	private(set) string $speedName = '';

	/** @var StatsUsagePokemon[] $pokemon */
	private(set) array $pokemon = [];

	/** @var DateTime[] $months */
	private(set) array $months = [];


	public function __construct(
		private(set) readonly DateModel $dateModel,
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
		$this->dateModel->setMonthAndFormat($month, $this->format->id);
		$thisMonth = $this->dateModel->thisMonth;
		$prevMonth = $this->dateModel->prevMonth;

		// Get the ratings for this month.
		$this->ratings = $this->ratingQueries->getByMonthAndFormat(
			$thisMonth,
			$this->format->id,
		);

		// Does leads data exist for this month?
		$this->showLeadsLink = $this->leadsRatedPokemonRepository->hasAny(
			$thisMonth,
			$this->format->id,
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
			$this->format->id,
			$rating,
			$languageId,
		);

		$this->months = $this->usageRatedQueries->getMonthsWithData(
			$this->format->id,
			$rating,
		);
	}
}
