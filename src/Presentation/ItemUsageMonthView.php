<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\ItemUsageMonth\ItemUsageData;
use Jp\Dex\Application\Models\ItemUsageMonth\ItemUsageMonthModel;
use Psr\Http\Message\ResponseInterface;
use Twig_Environment;
use Zend\Diactoros\Response;

class ItemUsageMonthView
{
	/** @var Twig_Environment $twig */
	private $twig;

	/** @var BaseView $baseView */
	private $baseView;

	/** @var ItemUsageMonthModel $itemUsageMonthModel */
	private $itemUsageMonthModel;

	/** @var IntlFormatterFactory $formatterFactory */
	private $formatterFactory;

	/**
	 * Constructor.
	 *
	 * @param Twig_Environment $twig
	 * @param BaseView $baseView
	 * @param ItemUsageMonthModel $itemUsageMonthModel
	 * @param IntlFormatterFactory $formatterFactory
	 */
	public function __construct(
		Twig_Environment $twig,
		BaseView $baseView,
		IntlFormatterFactory $formatterFactory,
		ItemUsageMonthModel $itemUsageMonthModel
	) {
		$this->twig = $twig;
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

		$content = $this->twig->render(
			'html/item-usage-month.twig',
			$this->baseView->getBaseVariables() + [
				// TODO: title - "Month Year Format Item usage stats"?
				'breadcrumbs' => $breadcrumbs,

				// The month control's data.
				'showPrevMonthLink' => $this->itemUsageMonthModel->doesPrevMonthDataExist(),
				'prevMonth' => $prevMonth->format('Y-m'),
				'prevMonthText' => $formatter->formatMonth($prevMonth),
				'showNextMonthLink' => $this->itemUsageMonthModel->doesNextMonthDataExist(),
				'nextMonth' => $nextMonth->format('Y-m'),
				'nextMonthText' => $formatter->formatMonth($nextMonth),
				'formatIdentifier' => $formatIdentifier,
				'rating' => $rating,
				'itemIdentifier' => $this->itemUsageMonthModel->getItemIdentifier(),

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
