<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\StatsItemModel;
use Laminas\Diactoros\Response\HtmlResponse;
use Psr\Http\Message\ResponseInterface;

final class StatsItemView
{
	private RendererInterface $renderer;
	private BaseView $baseView;
	private StatsItemModel $statsItemModel;
	private IntlFormatterFactory $formatterFactory;
	private MonthControlFormatter $monthControlFormatter;

	/**
	 * Constructor.
	 *
	 * @param RendererInterface $renderer
	 * @param BaseView $baseView
	 * @param StatsItemModel $statsItemModel
	 * @param IntlFormatterFactory $formatterFactory
	 * @param MonthControlFormatter $monthControlFormatter
	 */
	public function __construct(
		RendererInterface $renderer,
		BaseView $baseView,
		StatsItemModel $statsItemModel,
		IntlFormatterFactory $formatterFactory,
		MonthControlFormatter $monthControlFormatter
	) {
		$this->renderer = $renderer;
		$this->baseView = $baseView;
		$this->statsItemModel = $statsItemModel;
		$this->formatterFactory = $formatterFactory;
		$this->monthControlFormatter = $monthControlFormatter;
	}

	/**
	 * Get usage data to create a list of Pokémon who use a specific item.
	 *
	 * @return ResponseInterface
	 */
	public function getData() : ResponseInterface
	{
		$month = $this->statsItemModel->getMonth();
		$format = $this->statsItemModel->getFormat();
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
				'usageChangeText' => $formatter->formatChange($pokemon->getUsageChange()),
			];
		}

		// Navigation breadcrumbs.
		$formatIdentifier = $format->getIdentifier();
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
				'format' => [
					'identifier' => $format->getIdentifier(),
					'name' => $format->getName(),
				],
				'rating' => $rating,

				'breadcrumbs' => $breadcrumbs,

				'prevMonth' => $this->monthControlFormatter->format($prevMonth, $formatter),
				'nextMonth' => $this->monthControlFormatter->format($nextMonth, $formatter),
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
