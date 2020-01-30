<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\StatsMonth;

use Jp\Dex\Application\Models\DateModel;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Formats\FormatRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Stats\Usage\UsageRatedQueriesInterface;

final class StatsMonthModel
{
	private DateModel $dateModel;
	private UsageRatedQueriesInterface $usageRatedQueries;
	private FormatRepositoryInterface $formatRepository;


	private string $month;
	private LanguageId $languageId;

	/** @var FormatData[] $formatDatas */
	private array $formatDatas = [];


	/**
	 * Constructor.
	 *
	 * @param DateModel $dateModel
	 * @param UsageRatedQueriesInterface $usageRatedQueries
	 * @param FormatRepositoryInterface $formatRepository
	 */
	public function __construct(
		DateModel $dateModel,
		UsageRatedQueriesInterface $usageRatedQueries,
		FormatRepositoryInterface $formatRepository
	) {
		$this->dateModel = $dateModel;
		$this->usageRatedQueries = $usageRatedQueries;
		$this->formatRepository = $formatRepository;
	}

	/**
	 * Get the formats list to recreate a stats month directory, such as
	 * http://www.smogon.com/stats/2014-11.
	 *
	 * @param string $month
	 * @param LanguageId $languageId
	 *
	 * @return void
	 */
	public function setData(
		string $month,
		LanguageId $languageId
	) : void {
		$this->month = $month;
		$this->languageId = $languageId;

		// Get the previous month and the next month.
		$this->dateModel->setMonth($month);
		$thisMonth = $this->dateModel->getThisMonth();

		// Get the formats/ratings for this month.
		$formatRatings = $this->usageRatedQueries->getFormatRatings($thisMonth);

		// Re-organize the format/rating data.
		$formatIds = [];
		$ratings = [];
		foreach ($formatRatings as $formatRating) {
			/** @var FormatId $formatId */
			$formatId = $formatRating['formatId'];
			$rating = $formatRating['rating'];

			$formatIds[$formatId->value()] = $formatId;
			$ratings[$formatId->value()][] = $rating;
		}

		// Get additional data for each format.
		foreach ($formatIds as $formatId) {
			$format = $this->formatRepository->getById($formatId, $languageId);

			$this->formatDatas[] = new FormatData(
				$format->getIdentifier(),
				$format->getName(),
				$ratings[$formatId->value()]
			);
		}
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
	 * Get the month.
	 *
	 * @return string
	 */
	public function getMonth() : string
	{
		return $this->month;
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
	 * Get the format datas.
	 *
	 * @return FormatData[]
	 */
	public function getFormatDatas() : array
	{
		return $this->formatDatas;
	}
}
