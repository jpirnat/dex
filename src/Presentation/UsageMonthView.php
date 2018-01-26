<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\UsageMonth\UsageData;
use Jp\Dex\Application\Models\UsageMonth\UsageMonthModel;
use Psr\Http\Message\ResponseInterface;
use Twig_Environment;
use Zend\Diactoros\Response;

class UsageMonthView
{
	/** @var Twig_Environment $twig */
	private $twig;

	/** @var UsageMonthModel $usageMonthModel */
	private $usageMonthModel;

	/**
	 * Constructor.
	 *
	 * @param Twig_Environment $twig
	 * @param UsageMonthModel $usageMonthModel
	 */
	public function __construct(
		Twig_Environment $twig,
		UsageMonthModel $usageMonthModel
	) {
		$this->twig = $twig;
		$this->usageMonthModel = $usageMonthModel;
	}

	/**
	 * Get usage data to recreate a stats usage file, such as
	 * http://www.smogon.com/stats/2014-11/ou-1695.txt.
	 *
	 * @return ResponseInterface
	 */
	public function getData() : ResponseInterface
	{
		$year = $this->usageMonthModel->getYear();
		$month = $this->usageMonthModel->getMonth();
		$formatIdentifier = $this->usageMonthModel->getFormatIdentifier();
		$rating = $this->usageMonthModel->getRating();

		// Get the previous month and the next month.
		$prevMonth = $this->usageMonthModel->getDateModel()->getPrevMonth();
		$nextMonth = $this->usageMonthModel->getDateModel()->getNextMonth();

		// Get usage data and sort by rank.
		$usageDatas = $this->usageMonthModel->getUsageDatas();
		uasort(
			$usageDatas,
			function (UsageData $a, UsageData $b) : int {
				return $a->getRank() <=> $b->getRank();
			}
		);

		// Compile all usage data into the right form.
		$data = [];
		foreach ($usageDatas as $usageData) {
			$data[] = [
				'rank' => $usageData->getRank(),
				'name' => $usageData->getPokemonName(),
				'showMovesetLink' => $usageData->getUsagePercent() >= .01,
				'identifier' => $usageData->getPokemonIdentifier(),
				'formIcon' => $usageData->getFormIcon(),
				'usagePercent' => $usageData->getUsagePercent(),
				'usageChange' => $usageData->getUsageChange(),
				'raw' => $usageData->getRaw(),
				'rawPercent' => $usageData->getRawPercent(),
				'rawChange' => $usageData->getRawChange(),
				'real' => $usageData->getReal(),
				'realPercent' => $usageData->getRealPercent(),
				'realChange' => $usageData->getRealChange(),
			];
		}

		// Navigation breadcrumbs.
		$breadcrumbs = [
			[
				'url' => '/stats',
				'text' => 'Stats',
			],
			[
				'url' => "/stats/$year/$month",
				'text' => 'Formats',
			],
			[
				'text' => 'Usage',
			],
		];

		$content = $this->twig->render(
			'html/usage-month.twig',
			[
				// TODO: title - "Month Year Format usage stats"?
				'breadcrumbs' => $breadcrumbs,

				// The month control's data.
				'showPrevMonthLink' => $this->usageMonthModel->doesPrevMonthDataExist(),
				'prevYear' => $prevMonth->getYear(),
				'prevMonth' => $prevMonth->getMonth(),
				'showNextMonthLink' => $this->usageMonthModel->doesNextMonthDataExist(),
				'nextYear' => $nextMonth->getYear(),
				'nextMonth' => $nextMonth->getMonth(),
				'formatIdentifier' => $formatIdentifier,
				'rating' => $rating,

				'year' => $year,
				'month' => $month,

				// The main data.
				'data' => $data,
			]
		);

		$response = new Response();
		$response->getBody()->write($content);
		return $response;
	}
}
