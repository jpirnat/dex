<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\UsageMonth\UsageData;
use Jp\Dex\Application\Models\UsageMonth\UsageMonthModel;
use Psr\Http\Message\ResponseInterface;
use Twig_Environment;
use Zend\Diactoros\Response\HtmlResponse;

class UsageMonthView
{
	/** @var Twig_Environment $twig */
	private $twig;

	/** @var BaseView $baseView */
	private $baseView;

	/** @var UsageMonthModel $usageMonthModel */
	private $usageMonthModel;

	/** @var IntlFormatterFactory $formatterFactory */
	private $formatterFactory;

	/**
	 * Constructor.
	 *
	 * @param Twig_Environment $twig
	 * @param BaseView $baseView
	 * @param UsageMonthModel $usageMonthModel
	 * @param IntlFormatterFactory $formatterFactory
	 */
	public function __construct(
		Twig_Environment $twig,
		BaseView $baseView,
		UsageMonthModel $usageMonthModel,
		IntlFormatterFactory $formatterFactory
	) {
		$this->twig = $twig;
		$this->baseView = $baseView;
		$this->usageMonthModel = $usageMonthModel;
		$this->formatterFactory = $formatterFactory;
	}

	/**
	 * Get usage data to recreate a stats usage file, such as
	 * http://www.smogon.com/stats/2014-11/ou-1695.txt.
	 *
	 * @return ResponseInterface
	 */
	public function getData() : ResponseInterface
	{
		$month = $this->usageMonthModel->getMonth();
		$formatIdentifier = $this->usageMonthModel->getFormatIdentifier();
		$rating = $this->usageMonthModel->getRating();

		$formatter = $this->formatterFactory->createFor(
			$this->usageMonthModel->getLanguageId()
		);

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
				'usagePercent' => $formatter->formatPercent($usageData->getUsagePercent()),
				'usageChange' => $usageData->getUsageChange(),
				'usageChangeText' => $formatter->formatPercent($usageData->getUsageChange()),
				'raw' => $formatter->formatNumber($usageData->getRaw()),
				'rawPercent' => $formatter->formatPercent($usageData->getRawPercent()),
				'rawChange' => $usageData->getRawChange(),
				'rawChangeText' => $formatter->formatPercent($usageData->getRawChange()),
				'real' => $formatter->formatNumber($usageData->getReal()),
				'realPercent' => $formatter->formatPercent($usageData->getRealPercent()),
				'realChange' => $usageData->getRealChange(),
				'realChangeText' => $formatter->formatPercent($usageData->getRealChange()),
			];
		}

		// Navigation breadcrumbs.
		$breadcrumbs = [
			[
				'url' => '/stats',
				'text' => 'Stats',
			],
			[
				'url' => "/stats/$month",
				'text' => 'Formats',
			],
			[
				'text' => 'Usage',
			],
		];

		$content = $this->twig->render(
			'html/usage-month.twig',
			$this->baseView->getBaseVariables() + [
				'month' => $month,
				'formatIdentifier' => $formatIdentifier,
				'rating' => $rating,

				'breadcrumbs' => $breadcrumbs,

				'prevMonth' => [
					'show' => $this->usageMonthModel->doesPrevMonthDataExist(),
					'month' => $prevMonth->format('Y-m'),
					'text' => $formatter->formatMonth($prevMonth),
				],
				'nextMonth' => [
					'show' => $this->usageMonthModel->doesNextMonthDataExist(),
					'month' => $nextMonth->format('Y-m'),
					'text' => $formatter->formatMonth($nextMonth),
				],
				'ratings' => $this->usageMonthModel->getRatings(),

				'showLeadsLink' => $this->usageMonthModel->doesLeadsDataExist(),

				// The main data.
				'data' => $data,
			]
		);

		return new HtmlResponse($content);
	}
}
