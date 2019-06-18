<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\StatsMoveModel;
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

	/** @var MonthControlFormatter $monthControlFormatter */
	private $monthControlFormatter;

	/**
	 * Constructor.
	 *
	 * @param RendererInterface $renderer
	 * @param BaseView $baseView
	 * @param StatsMoveModel $statsMoveModel
	 * @param IntlFormatterFactory $formatterFactory
	 * @param MonthControlFormatter $monthControlFormatter
	 */
	public function __construct(
		RendererInterface $renderer,
		BaseView $baseView,
		StatsMoveModel $statsMoveModel,
		IntlFormatterFactory $formatterFactory,
		MonthControlFormatter $monthControlFormatter
	) {
		$this->renderer = $renderer;
		$this->baseView = $baseView;
		$this->statsMoveModel = $statsMoveModel;
		$this->formatterFactory = $formatterFactory;
		$this->monthControlFormatter = $monthControlFormatter;
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

		// Get the Pokémon usage data.
		$pokemonData = $this->statsMoveModel->getPokemon();
		$pokemons = [];
		foreach ($pokemonData as $pokemon) {
			$pokemons[] = [
				'icon' => $pokemon->getIcon(),
				'identifier' => $pokemon->getIdentifier(),
				'name' => $pokemon->getName(),
				'pokemonPercent' => $formatter->formatPercent($pokemon->getPokemonPercent()),
				'movePercent' => $formatter->formatPercent($pokemon->getMovePercent()),
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
				'text' => 'Moves',
			],
			[
				'text' => $this->statsMoveModel->getMoveName()->getName(),
			],
		];

		$content = $this->renderer->render(
			'html/stats/move.twig',
			$this->baseView->getBaseVariables() + [
				'month' => $month,
				'formatIdentifier' => $formatIdentifier,
				'rating' => $rating,

				'breadcrumbs' => $breadcrumbs,

				'prevMonth' => $this->monthControlFormatter->format($prevMonth, $formatter),
				'nextMonth' => $this->monthControlFormatter->format($nextMonth, $formatter),
				'ratings' => $this->statsMoveModel->getRatings(),

				'move' => [
					'identifier' => $this->statsMoveModel->getMoveIdentifier(),
					'name' => $this->statsMoveModel->getMoveName()->getName(),
					'description' => $this->statsMoveModel->getMoveDescription()->getDescription(),
				],


				// The main data.
				'pokemons' => $pokemons,
			]
		);

		return new HtmlResponse($content);
	}
}
