<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\MoveUsageMonth\MoveUsageData;
use Jp\Dex\Application\Models\MoveUsageMonth\MoveUsageMonthModel;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response\HtmlResponse;

class MoveUsageMonthView
{
	/** @var RendererInterface $renderer */
	private $renderer;

	/** @var BaseView $baseView */
	private $baseView;

	/** @var MoveUsageMonthModel $moveUsageMonthModel */
	private $moveUsageMonthModel;

	/** @var IntlFormatterFactory $formatterFactory */
	private $formatterFactory;

	/**
	 * Constructor.
	 *
	 * @param RendererInterface $renderer
	 * @param BaseView $baseView
	 * @param MoveUsageMonthModel $moveUsageMonthModel
	 * @param IntlFormatterFactory $formatterFactory
	 */
	public function __construct(
		RendererInterface $renderer,
		BaseView $baseView,
		IntlFormatterFactory $formatterFactory,
		MoveUsageMonthModel $moveUsageMonthModel
	) {
		$this->renderer = $renderer;
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
				'url' => "/stats/$month",
				'text' => 'Formats',
			],
			[
				'url' => "/stats/$month/$formatIdentifier/$rating",
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

		$content = $this->renderer->render(
			'html/move-usage-month.twig',
			$this->baseView->getBaseVariables() + [
				'month' => $month,
				'formatIdentifier' => $formatIdentifier,
				'rating' => $rating,

				'breadcrumbs' => $breadcrumbs,

				'prevMonth' => [
					'show' => $this->moveUsageMonthModel->doesPrevMonthDataExist(),
					'month' => $prevMonth->format('Y-m'),
					'text' => $formatter->formatMonth($prevMonth),
				],
				'nextMonth' => [
					'show' => $this->moveUsageMonthModel->doesNextMonthDataExist(),
					'month' => $nextMonth->format('Y-m'),
					'text' => $formatter->formatMonth($nextMonth),
				],
				'ratings' => $this->moveUsageMonthModel->getRatings(),

				'move' => [
					'identifier' => $this->moveUsageMonthModel->getMoveIdentifier(),
					'name' => $this->moveUsageMonthModel->getMoveName()->getName(),
					'description' => $this->moveUsageMonthModel->getMoveDescription()->getDescription(),
				],


				// The main data.
				'data' => $data,
			]
		);

		return new HtmlResponse($content);
	}
}
