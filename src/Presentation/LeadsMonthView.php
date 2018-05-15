<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\LeadsMonth\LeadsData;
use Jp\Dex\Application\Models\LeadsMonth\LeadsMonthModel;
use Psr\Http\Message\ResponseInterface;
use Twig_Environment;
use Zend\Diactoros\Response;

class LeadsMonthView
{
	/** @var Twig_Environment $twig */
	private $twig;

	/** @var BaseView $baseView */
	private $baseView;

	/** @var LeadsMonthModel $leadsMonthModel */
	private $leadsMonthModel;

	/** @var IntlFormatterFactory $formatterFactory */
	private $formatterFactory;

	/**
	 * Constructor.
	 *
	 * @param Twig_Environment $twig
	 * @param BaseView $baseView
	 * @param LeadsMonthModel $leadsMonthModel
	 * @param IntlFormatterFactory $formatterFactory
	 */
	public function __construct(
		Twig_Environment $twig,
		BaseView $baseView,
		LeadsMonthModel $leadsMonthModel,
		IntlFormatterFactory $formatterFactory
	) {
		$this->twig = $twig;
		$this->baseView = $baseView;
		$this->leadsMonthModel = $leadsMonthModel;
		$this->formatterFactory = $formatterFactory;
	}

	/**
	 * Get leads data to recreate a stats leads file, such as
	 * http://www.smogon.com/stats/leads/2014-11/ou-1695.txt.
	 *
	 * @return ResponseInterface
	 */
	public function getData() : ResponseInterface
	{
		$month = $this->leadsMonthModel->getMonth();
		$formatIdentifier = $this->leadsMonthModel->getFormatIdentifier();
		$rating = $this->leadsMonthModel->getRating();

		$formatter = $this->formatterFactory->createFor(
			$this->leadsMonthModel->getLanguageId()
		);

		// Get the previous month and the next month.
		$prevMonth = $this->leadsMonthModel->getDateModel()->getPrevMonth();
		$nextMonth = $this->leadsMonthModel->getDateModel()->getNextMonth();

		// Get usage data and sort by rank.
		$leadsDatas = $this->leadsMonthModel->getLeadsDatas();
		uasort(
			$leadsDatas,
			function (LeadsData $a, LeadsData $b) : int {
				return $a->getRank() <=> $b->getRank();
			}
		);

		// Compile all usage data into the right form.
		$data = [];
		foreach ($leadsDatas as $leadsData) {
			$data[] = [
				'rank' => $leadsData->getRank(),
				'name' => $leadsData->getPokemonName(),
				'showMovesetLink' => $leadsData->getUsagePercent() >= .01,
				'identifier' => $leadsData->getPokemonIdentifier(),
				'formIcon' => $leadsData->getFormIcon(),
				'usagePercent' => $formatter->formatPercent($leadsData->getLeadUsagePercent()),
				'usageChange' => $leadsData->getLeadUsageChange(),
				'usageChangeText' => $formatter->formatPercent($leadsData->getLeadUsageChange()),
				'raw' => $formatter->formatNumber($leadsData->getRaw()),
				'rawPercent' => $formatter->formatPercent($leadsData->getRawPercent()),
				'rawChange' => $leadsData->getRawChange(),
				'rawChangeText' => $formatter->formatPercent($leadsData->getRawChange()),
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
				'url' => "/stats/$month/$formatIdentifier/$rating",
				'text' => 'Usage',
			],
			[
				'text' => 'Leads',
			]
		];

		$content = $this->twig->render(
			'html/leads-month.twig',
			$this->baseView->getBaseVariables() + [
				// TODO: title - "Month Year Format lead usage stats"?
				'breadcrumbs' => $breadcrumbs,

				// The month control's data.
				'showPrevMonthLink' => $this->leadsMonthModel->doesPrevMonthDataExist(),
				'prevMonth' => $prevMonth->format('Y-m'),
				'prevMonthText' => $formatter->formatMonth($prevMonth),
				'showNextMonthLink' => $this->leadsMonthModel->doesNextMonthDataExist(),
				'nextMonth' => $nextMonth->format('Y-m'),
				'nextMonthText' => $formatter->formatMonth($nextMonth),
				'formatIdentifier' => $formatIdentifier,
				'rating' => $rating,

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
