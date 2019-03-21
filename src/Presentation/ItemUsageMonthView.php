<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\ItemUsageMonth\ItemUsageData;
use Jp\Dex\Application\Models\ItemUsageMonth\ItemUsageMonthModel;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response\HtmlResponse;

class ItemUsageMonthView
{
	/** @var RendererInterface $renderer */
	private $renderer;

	/** @var BaseView $baseView */
	private $baseView;

	/** @var ItemUsageMonthModel $itemUsageMonthModel */
	private $itemUsageMonthModel;

	/** @var IntlFormatterFactory $formatterFactory */
	private $formatterFactory;

	/**
	 * Constructor.
	 *
	 * @param RendererInterface $renderer
	 * @param BaseView $baseView
	 * @param ItemUsageMonthModel $itemUsageMonthModel
	 * @param IntlFormatterFactory $formatterFactory
	 */
	public function __construct(
		RendererInterface $renderer,
		BaseView $baseView,
		IntlFormatterFactory $formatterFactory,
		ItemUsageMonthModel $itemUsageMonthModel
	) {
		$this->renderer = $renderer;
		$this->baseView = $baseView;
		$this->itemUsageMonthModel = $itemUsageMonthModel;
		$this->formatterFactory = $formatterFactory;
	}

	/**
	 * Get usage data to create a list of Pokémon who use a specific item.
	 *
	 * @return ResponseInterface
	 */
	public function getData() : ResponseInterface
	{
		$month = $this->itemUsageMonthModel->getMonth();
		$formatIdentifier = $this->itemUsageMonthModel->getFormatIdentifier();
		$rating = $this->itemUsageMonthModel->getRating();

		$formatter = $this->formatterFactory->createFor(
			$this->itemUsageMonthModel->getLanguageId()
		);

		// Get the previous month and the next month.
		$prevMonth = $this->itemUsageMonthModel->getDateModel()->getPrevMonth();
		$nextMonth = $this->itemUsageMonthModel->getDateModel()->getNextMonth();

		// Get item usage data and sort by usage percent.
		$itemUsageDatas = $this->itemUsageMonthModel->getItemUsageDatas();
		uasort(
			$itemUsageDatas,
			function (ItemUsageData $a, ItemUsageData $b) : int {
				return $b->getUsagePercent() <=> $a->getUsagePercent();
			}
		);

		// Compile all item usage data into the right form.
		$data = [];
		foreach ($itemUsageDatas as $itemUsageData) {
			$data[] = [
				'name' => $itemUsageData->getPokemonName(),
				'identifier' => $itemUsageData->getPokemonIdentifier(),
				'formIcon' => $itemUsageData->getFormIcon(),
				'pokemonPercent' => $formatter->formatPercent($itemUsageData->getPokemonPercent()),
				'itemPercent' => $formatter->formatPercent($itemUsageData->getItemPercent()),
				'usagePercent' => $formatter->formatPercent($itemUsageData->getUsagePercent()),
				'change' => $itemUsageData->getChange(),
				'changeText' => $formatter->formatPercent($itemUsageData->getChange()),
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
				'text' => 'Items',
			],
			[
				'text' => $this->itemUsageMonthModel->getItemName()->getName(),
			],
		];

		$content = $this->renderer->render(
			'html/item-usage-month.twig',
			$this->baseView->getBaseVariables() + [
				'month' => $month,
				'formatIdentifier' => $formatIdentifier,
				'rating' => $rating,

				'breadcrumbs' => $breadcrumbs,

				'prevMonth' => [
					'show' => $this->itemUsageMonthModel->doesPrevMonthDataExist(),
					'month' => $prevMonth->format('Y-m'),
					'text' => $formatter->formatMonth($prevMonth),
				],
				'nextMonth' => [
					'show' => $this->itemUsageMonthModel->doesNextMonthDataExist(),
					'month' => $nextMonth->format('Y-m'),
					'text' => $formatter->formatMonth($nextMonth),
				],
				'ratings' => $this->itemUsageMonthModel->getRatings(),

				'item' => [
					'identifier' => $this->itemUsageMonthModel->getItemIdentifier(),
					'name' => $this->itemUsageMonthModel->getItemName()->getName(),
					'description' => $this->itemUsageMonthModel->getItemDescription()->getDescription(),
				],

				// The main data.
				'data' => $data,
			]
		);

		return new HtmlResponse($content);
	}
}
