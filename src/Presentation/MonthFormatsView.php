<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\MonthFormats\FormatData;
use Jp\Dex\Application\Models\MonthFormats\MonthFormatsModel;
use Psr\Http\Message\ResponseInterface;
use Twig_Environment;
use Zend\Diactoros\Response;

class MonthFormatsView
{
	/** @var Twig_Environment $twig */
	private $twig;

	/** @var BaseView $baseView */
	private $baseView;

	/** @var MonthFormatsModel $monthFormatsModel */
	private $monthFormatsModel;

	/** @var IntlFormatterFactory $formatterFactory */
	private $formatterFactory;

	/**
	 * Constructor.
	 *
	 * @param Twig_Environment $twig
	 * @param BaseView $baseView
	 * @param MonthFormatsModel $monthFormatsModel
	 * @param IntlFormatterFactory $formatterFactory
	 */
	public function __construct(
		Twig_Environment $twig,
		BaseView $baseView,
		MonthFormatsModel $monthFormatsModel,
		IntlFormatterFactory $formatterFactory
	) {
		$this->twig = $twig;
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

		$content = $this->twig->render(
			'html/month-formats.twig',
			$this->baseView->getBaseVariables() + [
				// TODO: title - "Month Year formats"?
				'breadcrumbs' => $breadcrumbs,

				// The month control's data.
				'showPrevMonthLink' => $this->monthFormatsModel->doesPrevMonthDataExist(),
				'prevMonth' => $prevMonth->format('Y-m'),
				'prevMonthText' => $formatter->formatMonth($prevMonth),
				'showNextMonthLink' => $this->monthFormatsModel->doesNextMonthDataExist(),
				'nextMonth' => $nextMonth->format('Y-m'),
				'nextMonthText' => $formatter->formatMonth($nextMonth),

				'month' => $month,

				// The main data.
				'formats' => $formats,
			]
		);

		$response = new Response();
		$response->getBody()->write($content);
		return $response;
	}
}
