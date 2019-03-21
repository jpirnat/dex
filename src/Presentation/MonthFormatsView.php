<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\MonthFormats\FormatData;
use Jp\Dex\Application\Models\MonthFormats\MonthFormatsModel;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response\HtmlResponse;

class MonthFormatsView
{
	/** @var RendererInterface $renderer */
	private $renderer;

	/** @var BaseView $baseView */
	private $baseView;

	/** @var MonthFormatsModel $monthFormatsModel */
	private $monthFormatsModel;

	/** @var IntlFormatterFactory $formatterFactory */
	private $formatterFactory;

	/**
	 * Constructor.
	 *
	 * @param RendererInterface $renderer
	 * @param BaseView $baseView
	 * @param MonthFormatsModel $monthFormatsModel
	 * @param IntlFormatterFactory $formatterFactory
	 */
	public function __construct(
		RendererInterface $renderer,
		BaseView $baseView,
		MonthFormatsModel $monthFormatsModel,
		IntlFormatterFactory $formatterFactory
	) {
		$this->renderer = $renderer;
		$this->baseView = $baseView;
		$this->monthFormatsModel = $monthFormatsModel;
		$this->formatterFactory = $formatterFactory;
	}

	/**
	 * Get the formats list to recreate a stats month directory, such as
	 * http://www.smogon.com/stats/2014-11.
	 *
	 * @return ResponseInterface
	 */
	public function index() : ResponseInterface
	{
		$month = $this->monthFormatsModel->getMonth();

		$formatter = $this->formatterFactory->createFor(
			$this->monthFormatsModel->getLanguageId()
		);

		// Get the previous month and the next month.
		$prevMonth = $this->monthFormatsModel->getDateModel()->getPrevMonth();
		$nextMonth = $this->monthFormatsModel->getDateModel()->getNextMonth();

		// Get format data and sort by name.
		$formatDatas = $this->monthFormatsModel->getFormatDatas();
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
			'html/month-formats.twig',
			$this->baseView->getBaseVariables() + [
				// TODO: title - "Month Year formats"?
				'breadcrumbs' => $breadcrumbs,

				'prevMonth' => [
					'show' => $this->monthFormatsModel->doesPrevMonthDataExist(),
					'month' => $prevMonth->format('Y-m'),
					'text' => $formatter->formatMonth($prevMonth),
				],
				'nextMonth' => [
					'show' => $this->monthFormatsModel->doesNextMonthDataExist(),
					'month' => $nextMonth->format('Y-m'),
					'text' => $formatter->formatMonth($nextMonth),
				],

				'month' => $month,

				// The main data.
				'formats' => $formats,
			]
		);

		return new HtmlResponse($content);
	}
}
