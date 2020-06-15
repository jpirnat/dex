<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\StatsMonth\FormatData;
use Jp\Dex\Application\Models\StatsMonth\StatsMonthModel;
use Laminas\Diactoros\Response\HtmlResponse;
use Psr\Http\Message\ResponseInterface;

final class StatsMonthView
{
	private RendererInterface $renderer;
	private BaseView $baseView;
	private StatsMonthModel $statsMonthModel;
	private IntlFormatterFactory $formatterFactory;
	private MonthControlFormatter $monthControlFormatter;

	/**
	 * Constructor.
	 *
	 * @param RendererInterface $renderer
	 * @param BaseView $baseView
	 * @param StatsMonthModel $statsMonthModel
	 * @param IntlFormatterFactory $formatterFactory
	 * @param MonthControlFormatter $monthControlFormatter
	 */
	public function __construct(
		RendererInterface $renderer,
		BaseView $baseView,
		StatsMonthModel $statsMonthModel,
		IntlFormatterFactory $formatterFactory,
		MonthControlFormatter $monthControlFormatter
	) {
		$this->renderer = $renderer;
		$this->baseView = $baseView;
		$this->statsMonthModel = $statsMonthModel;
		$this->formatterFactory = $formatterFactory;
		$this->monthControlFormatter = $monthControlFormatter;
	}

	/**
	 * Get the formats list to recreate a stats month directory, such as
	 * http://www.smogon.com/stats/2014-11.
	 *
	 * @return ResponseInterface
	 */
	public function index() : ResponseInterface
	{
		$formatter = $this->formatterFactory->createFor(
			$this->statsMonthModel->getLanguageId()
		);

		// Get the previous month and the next month.
		$dateModel = $this->statsMonthModel->getDateModel();
		$prevMonth = $dateModel->getPrevMonth();
		$thisMonth = $dateModel->getThisMonth();
		$nextMonth = $dateModel->getNextMonth();
		$prevMonth = $this->monthControlFormatter->format($prevMonth, $formatter);
		$thisMonth = $this->monthControlFormatter->format($thisMonth, $formatter);
		$nextMonth = $this->monthControlFormatter->format($nextMonth, $formatter);

		// Get format data and sort by name.
		$formatDatas = $this->statsMonthModel->getFormatDatas();
		uasort(
			$formatDatas,
			function (FormatData $a, FormatData $b) : int {
				return $a->getFormatName() <=> $b->getFormatName();
			}
		);

		// Compile all usage data into the right form.
		$formats = [];
		foreach ($formatDatas as $formatData) {
			$formats[] = [
				'identifier' => $formatData->getFormatIdentifier(),
				'name' => $formatData->getFormatName(),
				'ratings' => $formatData->getRatings(),
			];
		}

		// Navigation breadcrumbs.
		$breadcrumbs = [
			[
				'url' => '/stats',
				'text' => 'Stats',
			],
			[
				'text' => $thisMonth['text'],
			]
		];

		$content = $this->renderer->render(
			'html/stats/month.twig',
			$this->baseView->getBaseVariables() + [
				// TODO: title - "Month Year formats"?
				'breadcrumbs' => $breadcrumbs,

				'prevMonth' => $prevMonth,
				'thisMonth' => $thisMonth,
				'nextMonth' => $nextMonth,

				// The main data.
				'formats' => $formats,
			]
		);

		return new HtmlResponse($content);
	}
}
