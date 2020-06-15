<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Trends\Generators;

use DateInterval;
use DatePeriod;
use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Stats\Trends\Lines\TrendPoint;
use Jp\Dex\Domain\Stats\Usage\UsageQueriesInterface;
use Jp\Dex\Domain\Stats\UsageDataInterface;

final class TrendPointCalculator
{
	private UsageQueriesInterface $usageQueries;

	/**
	 * Constructor.
	 *
	 * @param UsageQueriesInterface $usageQueries
	 */
	public function __construct(UsageQueriesInterface $usageQueries)
	{
		$this->usageQueries = $usageQueries;
	}

	/**
	 * Get the first date in this array of usage datas. Assume the array is
	 * indexed and sorted by month.
	 *
	 * @param UsageDataInterface[] $usageDatas
	 * @param FormatId $formatId
	 *
	 * @return DateTime
	 */
	private function getFirstDate(array $usageDatas, FormatId $formatId) : DateTime
	{
		if ($usageDatas === []) {
			// If there's no usage data for this request, let's default our
			// trend span to the entire history of the format.
			$month = $this->usageQueries->getOldest($formatId);

			if ($month === null) {
				// If the format has no usage data at all, just do this.
				return new DateTime('first day of last month');
			}

			return $month;
		}

		$usageData = reset($usageDatas);
		return $usageData->getMonth();
	}

	/**
	 * Get the final date in this array of usage datas. Assume the array is
	 * indexed and sorted by month.
	 *
	 * @param UsageDataInterface[] $usageDatas
	 * @param FormatId $formatId
	 *
	 * @return DateTime
	 */
	private function getFinalDate(array $usageDatas, FormatId $formatId) : DateTime
	{
		if ($usageDatas === []) {
			// If there's no usage data for this request, let's default our
			// trend span to the entire history of the format.
			$month = $this->usageQueries->getNewest($formatId);

			if ($month === null) {
				// If the format has no usage data at all, just do this.
				return new DateTime('first day of last month');
			}

			return $month;
		}

		$usageData = end($usageDatas);
		return $usageData->getMonth();
	}

	/**
	 * Get the trend points from this series of usage datas.
	 *
	 * @param FormatId $formatId The format the data is from.
	 * @param UsageDataInterface[] $usageDatas
	 * @param string $method The method to call on a usage data object to get
	 *     the point's value.
	 * @param float $default The default value for points without data.
	 *
	 * @return TrendPoint[]
	 */
	public function getTrendPoints(
		FormatId $formatId,
		array $usageDatas,
		string $method,
		float $default
	) : array {
		// Get the first and final dates in the series.
		$firstDate = $this->getFirstDate($usageDatas, $formatId);
		$finalDate = $this->getFinalDate($usageDatas, $formatId)->modify('+1 second');

		// Iterate through each month in the series.
		$period = new DatePeriod($firstDate, new DateInterval('P1M'), $finalDate);

		$trendPoints = [];

		/** @var DateTime $date */
		foreach ($period as $date) {
			$month = $date->format('Y-m-d');

			// Get this month's usage percent, if one exists.
			$value = $default;
			if (isset($usageDatas[$month])) {
				$usageData = $usageDatas[$month];
				$value = $usageData->$method();
			}

			$trendPoints[] = new TrendPoint($date, $value);
		}

		return $trendPoints;
	}
}
