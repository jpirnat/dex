<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\StatsItemModel;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response\HtmlResponse;

class StatsItemView
{
	/** @var RendererInterface $renderer */
	private $renderer;

	/** @var BaseView $baseView */
	private $baseView;

	/** @var StatsItemModel $statsItemModel */
	private $statsItemModel;

	/** @var IntlFormatterFactory $formatterFactory */
	private $formatterFactory;

	/**
	 * Constructor.
	 *
	 * @param RendererInterface $renderer
	 * @param BaseView $baseView
	 * @param StatsItemModel $statsItemModel
	 * @param IntlFormatterFactory $formatterFactory
	 */
	public function __construct(
		RendererInterface $renderer,
		BaseView $baseView,
		IntlFormatterFactory $formatterFactory,
		StatsItemModel $statsItemModel
	) {
		$this->renderer = $renderer;
		$this->baseView = $baseView;
		$this->statsItemModel = $statsItemModel;
		$this->formatterFactory = $formatterFactory;
	}

	/**
	 * Get usage data to create a list of Pokémon who use a specific item.
	 *
	 * @return ResponseInterface
	 */
	public function getData() : ResponseInterface
	{
		$month = $this->statsItemModel->getMonth();
		$formatIdentifier = $this->statsItemModel->getFormatIdentifier();
		$rating = $this->statsItemModel->getRating();

		$formatter = $this->formatterFactory->createFor(
			$this->statsItemModel->getLanguageId()
		);

		// Get the previous month and the next month.
		$prevMonth = $this->statsItemModel->getDateModel()->getPrevMonth();
		$nextMonth = $this->statsItemModel->getDateModel()->getNextMonth();

		// Get the Pokémon usage data.
		$pokemonData = $this->statsItemModel->getPokemon();
		$pokemons = [];
		foreach ($pokemonData as $pokemon) {
			$pokemons[] = [
				'icon' => $pokemon->getIcon(),
				'identifier' => $pokemon->getIdentifier(),
				'name' => $pokemon->getName(),
				'pokemonPercent' => $formatter->formatPercent($pokemon->getPokemonPercent()),
				'itemPercent' => $formatter->formatPercent($pokemon->getItemPercent()),
				'usagePercent' => $formatter->formatPercent($pokemon->getUsagePercent()),
				'usageChange' => $pokemon->getUsageChange(),
				'usageChangeText' => $formatter->formatPercent($pokemon->getUsageChange()),
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
				'text' => $this->statsItemModel->getItemName()->getName(),
			],
		];

		$content = $this->renderer->render(
			'html/stats/item.twig',
			$this->baseView->getBaseVariables() + [
				'month' => $month,
				'formatIdentifier' => $formatIdentifier,
				'rating' => $rating,

				'breadcrumbs' => $breadcrumbs,

				'prevMonth' => [
					'show' => $this->statsItemModel->doesPrevMonthDataExist(),
					'month' => $prevMonth->format('Y-m'),
					'text' => $formatter->formatMonth($prevMonth),
				],
				'nextMonth' => [
					'show' => $this->statsItemModel->doesNextMonthDataExist(),
					'month' => $nextMonth->format('Y-m'),
					'text' => $formatter->formatMonth($nextMonth),
				],
				'ratings' => $this->statsItemModel->getRatings(),

				'item' => [
					'identifier' => $this->statsItemModel->getItemIdentifier(),
					'name' => $this->statsItemModel->getItemName()->getName(),
					'description' => $this->statsItemModel->getItemDescription()->getDescription(),
				],

				// The main data.
				'pokemons' => $pokemons,
			]
		);

		return new HtmlResponse($content);
	}
}
