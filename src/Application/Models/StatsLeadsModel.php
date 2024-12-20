<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use DateTime;
use Jp\Dex\Domain\Formats\Format;
use Jp\Dex\Domain\Formats\FormatRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Leads\StatsLeadsPokemon;
use Jp\Dex\Domain\Leads\StatsLeadsPokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\StatId;
use Jp\Dex\Domain\Stats\StatNameRepositoryInterface;
use Jp\Dex\Domain\Stats\Usage\RatingQueriesInterface;
use Jp\Dex\Domain\Stats\Usage\UsageRatedQueriesInterface;

final class StatsLeadsModel
{
	private(set) string $month;
	private(set) Format $format;
	private(set) int $rating;
	private(set) LanguageId $languageId;

	/** @var int[] $ratings */
	private(set) array $ratings = [];

	private(set) string $speedName = '';

	/** @var StatsLeadsPokemon[] $pokemon */
	private(set) array $pokemon = [];

	/** @var DateTime[] $months */
	private(set) array $months = [];


	public function __construct(
		private(set) readonly DateModel $dateModel,
		private readonly FormatRepositoryInterface $formatRepository,
		private readonly RatingQueriesInterface $ratingQueries,
		private readonly StatNameRepositoryInterface $statNameRepository,
		private readonly StatsLeadsPokemonRepositoryInterface $statsLeadsPokemonRepository,
		private readonly UsageRatedQueriesInterface $usageRatedQueries,
	) {}


	/**
	 * Get leads data to recreate a stats leads file, such as
	 * http://www.smogon.com/stats/leads/2014-11/ou-1695.txt.
	 */
	public function setData(
		string $month,
		string $formatIdentifier,
		int $rating,
		LanguageId $languageId,
	) : void {
		$this->month = $month;
		$this->rating = $rating;
		$this->languageId = $languageId;

		// Get the format.
		$this->format = $this->formatRepository->getByIdentifier(
			$formatIdentifier,
			$languageId,
		);

		// Get the previous month and the next month.
		$this->dateModel->setMonthAndFormat($month, $this->format->getId());
		$thisMonth = $this->dateModel->thisMonth;
		$prevMonth = $this->dateModel->prevMonth;

		// Get the ratings for this month.
		$this->ratings = $this->ratingQueries->getByMonthAndFormat(
			$thisMonth,
			$this->format->getId(),
		);

		$speedName = $this->statNameRepository->getByLanguageAndStat(
			$languageId,
			new StatId(StatId::SPEED),
		);
		$this->speedName = $speedName->getName();

		// Get the Pokémon usage data.
		$this->pokemon = $this->statsLeadsPokemonRepository->getByMonth(
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
}
