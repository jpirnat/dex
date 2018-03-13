<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\MoveUsageMonth\MoveUsageData;
use Jp\Dex\Application\Models\MoveUsageMonth\MoveUsageMonthModel;
use Psr\Http\Message\ResponseInterface;
use Twig_Environment;
use Zend\Diactoros\Response;

class MoveUsageMonthView
{
	/** @var Twig_Environment $twig */
	private $twig;

	/** @var BaseView $baseView */
	private $baseView;

	/** @var MoveUsageMonthModel $moveUsageMonthModel */
	private $moveUsageMonthModel;

	/** @var IntlFormatterFactory $formatterFactory */
	private $formatterFactory;

	/**
	 * Constructor.
	 *
	 * @param Twig_Environment $twig
	 * @param BaseView $baseView
	 * @param MoveUsageMonthModel $moveUsageMonthModel
	 * @param IntlFormatterFactory $formatterFactory
	 */
	public function __construct(
		Twig_Environment $twig,
		BaseView $baseView,
		IntlFormatterFactory $formatterFactory,
		MoveUsageMonthModel $moveUsageMonthModel
	) {
		$this->twig = $twig;
		$this->baseView = $baseView;
		$this->moveUsageMonthModel = $moveUsageMonthModel;
		$this->formatterFactory = $formatterFactory;
	}

	/**
	 * Get usage data to create a list of Pokémon who use a specific move.
	 *
	 * @return ResponseInterface
	 */
	public function getData() : ResponseInterface
	{
		$year = $this->moveUsageMonthModel->getYear();
		$month = $this->moveUsageMonthModel->getMonth();
		$formatIdentifier = $this->moveUsageMonthModel->getFormatIdentifier();
		$rating = $this->moveUsageMonthModel->getRating();

		$formatter = $this->formatterFactory->createFor(
			$this->moveUsageMonthModel->getLanguageId()
		);

		// Get the previous month and the next month.
		$prevMonth = $this->moveUsageMonthModel->getDateModel()->getPrevMonth();
		$nextMonth = $this->moveUsageMonthModel->getDateModel()->getNextMonth();

		// Get move usage data and sort by usage percent.
		$moveUsageDatas = $this->moveUsageMonthModel->getMoveUsageDatas();
		uasort(
			$moveUsageDatas,
			function (MoveUsageData $a, MoveUsageData $b) : int {
				return $b->getUsagePercent() <=> $a->getUsagePercent();
			}
		);

		// Compile all move usage data into the right form.
		$data = [];
		foreach ($moveUsageDatas as $moveUsageData) {
			$data[] = [
				'name' => $moveUsageData->getPokemonName(),
				'identifier' => $moveUsageData->getPokemonIdentifier(),
				'formIcon' => $moveUsageData->getFormIcon(),
				'pokemonPercent' => $formatter->formatPercent($moveUsageData->getPokemonPercent()),
				'movePercent' => $formatter->formatPercent($moveUsageData->getMovePercent()),
				'usagePercent' => $formatter->formatPercent($moveUsageData->getUsagePercent()),
				'change' => $moveUsageData->getChange(),
				'changeText' => $formatter->formatPercent($moveUsageData->getChange()),
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
				'url' => "/stats/$year/$month/$formatIdentifier/$rating",
				'text' => 'Usage',
			],
			[
				// TODO: url
				'text' => 'Moves',
			],
			[
				'text' => $this->moveUsageMonthModel->getMoveName()->getName(),
			],
		];

		$content = $this->twig->render(
			'html/move-usage-month.twig',
			$this->baseView->getBaseVariables() + [
				// TODO: title - "Month Year Format Move usage stats"?
				'breadcrumbs' => $breadcrumbs,

				// The month control's data.
				'showPrevMonthLink' => $this->moveUsageMonthModel->doesPrevMonthDataExist(),
				'prevYear' => $prevMonth->getYear(),
				'prevMonth' => $prevMonth->getMonth(),
				'prevMonthText' => $formatter->formatYearMonth($prevMonth),
				'showNextMonthLink' => $this->moveUsageMonthModel->doesNextMonthDataExist(),
				'nextYear' => $nextMonth->getYear(),
				'nextMonth' => $nextMonth->getMonth(),
				'nextMonthText' => $formatter->formatYearMonth($nextMonth),
				'formatIdentifier' => $formatIdentifier,
				'rating' => $rating,
				'moveIdentifier' => $this->moveUsageMonthModel->getMoveIdentifier(),

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
