<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\StatsMonth\FormatData;
use Jp\Dex\Application\Models\StatsMonth\StatsMonthModel;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response\HtmlResponse;

class StatsMonthView
{
	/** @var RendererInterface $renderer */
	private $renderer;

	/** @var BaseView $baseView */
	private $baseView;

	/** @var StatsMonthModel $statsMonthModel */
	private $statsMonthModel;

	/** @var IntlFormatterFactory $formatterFactory */
	private $formatterFactory;

	/** @var MonthControlFormatter $monthControlFormatter */
	private $monthControlFormatter;

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
		$month = $this->statsMonthModel->getMonth();

		$formatter = $this->formatterFactory->createFor(
			$this->statsMonthModel->getLanguageId()
		);

		// Get the previous month and the next month.
		$prevMonth = $this->statsMonthModel->getDateModel()->getPrevMonth();
		$nextMonth = $this->statsMonthModel->getDateModel()->getNextMonth();

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
				'text' => 'Formats',
			]
		];

		$content = $this->renderer->render(
			'html/stats/month.twig',
			$this->baseView->getBaseVariables() + [
				// TODO: title - "Month Year formats"?
				'breadcrumbs' => $breadcrumbs,

				'prevMonth' => $this->monthControlFormatter->format($prevMonth, $formatter),
				'nextMonth' => $this->monthControlFormatter->format($nextMonth, $formatter),

				'month' => $month,

				// The main data.
				'formats' => $formats,
			]
		);

		return new HtmlResponse($content);
	}
}
