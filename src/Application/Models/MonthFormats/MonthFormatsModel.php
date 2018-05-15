<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\MonthFormats;

use Jp\Dex\Application\Models\DateModel;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Formats\FormatNameRepositoryInterface;
use Jp\Dex\Domain\Formats\FormatRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Stats\Usage\UsageRatedQueriesInterface;
use Jp\Dex\Domain\Stats\Usage\UsageRepositoryInterface;

class MonthFormatsModel
{
	/** @var DateModel $dateModel */
	private $dateModel;

	/** @var UsageRatedQueriesInterface $usageRatedQueries */
	private $usageRatedQueries;

	/** @var FormatRepositoryInterface $formatRepository */
	private $formatRepository;

	/** @var FormatNameRepositoryInterface $formatNameRepository */
	private $formatNameRepository;

	/** @var UsageRepositoryInterface $usageRepository */
	private $usageRepository;


	/** @var bool $prevMonthDataExists */
	private $prevMonthDataExists;

	/** @var bool $nextMonthDataExists */
	private $nextMonthDataExists;

	/** @var string $month */
	private $month;

	/** @var LanguageId $languageId */
	private $languageId;

	/** @var FormatData[] $formatDatas */
	private $formatDatas = [];

	/**
	 * Constructor.
	 *
	 * @param DateModel $dateModel
	 * @param UsageRatedQueriesInterface $usageRatedQueries
	 * @param FormatRepositoryInterface $formatRepository
	 * @param FormatNameRepositoryInterface $formatNameRepository
	 * @param UsageRepositoryInterface $usageRepository
	 */
	public function __construct(
		DateModel $dateModel,
		UsageRatedQueriesInterface $usageRatedQueries,
		FormatRepositoryInterface $formatRepository,
		FormatNameRepositoryInterface $formatNameRepository,
		UsageRepositoryInterface $usageRepository
	) {
		$this->dateModel = $dateModel;
		$this->usageRatedQueries = $usageRatedQueries;
		$this->formatRepository = $formatRepository;
		$this->formatNameRepository = $formatNameRepository;
		$this->usageRepository = $usageRepository;
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
		$this->dateModel->setData($month);
		$thisMonth = $this->dateModel->getThisMonth();
		$prevMonth = $this->dateModel->getPrevMonth();
		$nextMonth = $this->dateModel->getNextMonth();

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
			$format = $this->formatRepository->getById($formatId);

			$formatName = $this->formatNameRepository->getByLanguageAndFormat(
				$languageId,
				$formatId
			);

			$this->formatDatas[] = new FormatData(
				$format->getIdentifier(),
				$formatName->getName(),
				$ratings[$formatId->value()]
			);
		}

		// Does usage data exist for the previous month?
		$this->prevMonthDataExists = $this->usageRepository->hasAny($prevMonth);

		// Does usage data exist for the next month?
		$this->nextMonthDataExists = $this->usageRepository->hasAny($nextMonth);
	}

	/**
	 * Does usage data exist for the previous month?
	 *
	 * @return bool
	 */
	public function doesPrevMonthDataExist() : bool
	{
		return $this->prevMonthDataExists;
	}

	/**
	 * Does usage data exist for the next month?
	 *
	 * @return bool
	 */
	public function doesNextMonthDataExist() : bool
	{
		return $this->nextMonthDataExists;
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
