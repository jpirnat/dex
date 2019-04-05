<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\StatsMove\MoveUsageData;
use Jp\Dex\Application\Models\StatsMove\StatsMoveModel;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response\HtmlResponse;

class StatsMoveView
{
	/** @var RendererInterface $renderer */
	private $renderer;

	/** @var BaseView $baseView */
	private $baseView;

	/** @var StatsMoveModel $statsMoveModel */
	private $statsMoveModel;

	/** @var IntlFormatterFactory $formatterFactory */
	private $formatterFactory;

	/**
	 * Constructor.
	 *
	 * @param RendererInterface $renderer
	 * @param BaseView $baseView
	 * @param StatsMoveModel $statsMoveModel
	 * @param IntlFormatterFactory $formatterFactory
	 */
	public function __construct(
		RendererInterface $renderer,
		BaseView $baseView,
		IntlFormatterFactory $formatterFactory,
		StatsMoveModel $statsMoveModel
	) {
		$this->renderer = $renderer;
		$this->baseView = $baseView;
		$this->statsMoveModel = $statsMoveModel;
		$this->formatterFactory = $formatterFactory;
	}

	/**
	 * Get usage data to create a list of Pokémon who use a specific move.
	 *
	 * @return ResponseInterface
	 */
	public function getData() : ResponseInterface
	{
		$month = $this->statsMoveModel->getMonth();
		$formatIdentifier = $this->statsMoveModel->getFormatIdentifier();
		$rating = $this->statsMoveModel->getRating();

		$formatter = $this->formatterFactory->createFor(
			$this->statsMoveModel->getLanguageId()
		);

		// Get the previous month and the next month.
		$prevMonth = $this->statsMoveModel->getDateModel()->getPrevMonth();
		$nextMonth = $this->statsMoveModel->getDateModel()->getNextMonth();

		// Get move usage data and sort by usage percent.
		$moveUsageDatas = $this->statsMoveModel->getMoveUsageDatas();
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
				'text' => $this->statsMoveModel->getMoveName()->getName(),
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
					'show' => $this->statsMoveModel->doesPrevMonthDataExist(),
					'month' => $prevMonth->format('Y-m'),
					'text' => $formatter->formatMonth($prevMonth),
				],
				'nextMonth' => [
					'show' => $this->statsMoveModel->doesNextMonthDataExist(),
					'month' => $nextMonth->format('Y-m'),
					'text' => $formatter->formatMonth($nextMonth),
				],
				'ratings' => $this->statsMoveModel->getRatings(),

				'move' => [
					'identifier' => $this->statsMoveModel->getMoveIdentifier(),
					'name' => $this->statsMoveModel->getMoveName()->getName(),
					'description' => $this->statsMoveModel->getMoveDescription()->getDescription(),
				],


				// The main data.
				'data' => $data,
			]
		);

		return new HtmlResponse($content);
	}
}
