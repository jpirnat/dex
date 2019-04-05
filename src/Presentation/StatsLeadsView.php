<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\StatsLeads\LeadsData;
use Jp\Dex\Application\Models\StatsLeads\StatsLeadsModel;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response\HtmlResponse;

class StatsLeadsView
{
	/** @var RendererInterface $renderer */
	private $renderer;

	/** @var BaseView $baseView */
	private $baseView;

	/** @var StatsLeadsModel $statsLeadsModel */
	private $statsLeadsModel;

	/** @var IntlFormatterFactory $formatterFactory */
	private $formatterFactory;

	/**
	 * Constructor.
	 *
	 * @param RendererInterface $renderer
	 * @param BaseView $baseView
	 * @param StatsLeadsModel $statsLeadsModel
	 * @param IntlFormatterFactory $formatterFactory
	 */
	public function __construct(
		RendererInterface $renderer,
		BaseView $baseView,
		StatsLeadsModel $statsLeadsModel,
		IntlFormatterFactory $formatterFactory
	) {
		$this->renderer = $renderer;
		$this->baseView = $baseView;
		$this->statsLeadsModel = $statsLeadsModel;
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
		$month = $this->statsLeadsModel->getMonth();
		$formatIdentifier = $this->statsLeadsModel->getFormatIdentifier();
		$rating = $this->statsLeadsModel->getRating();

		$formatter = $this->formatterFactory->createFor(
			$this->statsLeadsModel->getLanguageId()
		);

		// Get the previous month and the next month.
		$prevMonth = $this->statsLeadsModel->getDateModel()->getPrevMonth();
		$nextMonth = $this->statsLeadsModel->getDateModel()->getNextMonth();

		// Get usage data and sort by rank.
		$leadsDatas = $this->statsLeadsModel->getLeadsDatas();
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

		$content = $this->renderer->render(
			'html/leads-month.twig',
			$this->baseView->getBaseVariables() + [
				'month' => $month,
				'formatIdentifier' => $formatIdentifier,
				'rating' => $rating,

				'breadcrumbs' => $breadcrumbs,

				'prevMonth' => [
					'show' => $this->statsLeadsModel->doesPrevMonthDataExist(),
					'month' => $prevMonth->format('Y-m'),
					'text' => $formatter->formatMonth($prevMonth),
				],
				'nextMonth' => [
					'show' => $this->statsLeadsModel->doesNextMonthDataExist(),
					'month' => $nextMonth->format('Y-m'),
					'text' => $formatter->formatMonth($nextMonth),
				],
				'ratings' => $this->statsLeadsModel->getRatings(),

				// The main data.
				'data' => $data,
			]
		);

		return new HtmlResponse($content);
	}
}
