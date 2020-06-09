<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\StatsMoveModel;
use Laminas\Diactoros\Response\HtmlResponse;
use Psr\Http\Message\ResponseInterface;

final class StatsMoveView
{
	private RendererInterface $renderer;
	private BaseView $baseView;
	private StatsMoveModel $statsMoveModel;
	private IntlFormatterFactory $formatterFactory;
	private MonthControlFormatter $monthControlFormatter;

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
		$format = $this->statsMoveModel->getFormat();
		$rating = $this->statsMoveModel->getRating();

		$formatter = $this->formatterFactory->createFor(
			$this->statsMoveModel->getLanguageId()
		);

		// Get the previous month and the next month.
		$dateModel = $this->statsMoveModel->getDateModel();
		$prevMonth = $dateModel->getPrevMonth();
		$thisMonth = $dateModel->getThisMonth();
		$nextMonth = $dateModel->getNextMonth();
		$prevMonth = $this->monthControlFormatter->format($prevMonth, $formatter);
		$thisMonth = $this->monthControlFormatter->format($thisMonth, $formatter);
		$nextMonth = $this->monthControlFormatter->format($nextMonth, $formatter);

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
				'text' => $thisMonth['text'],
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
				'format' => [
					'identifier' => $format->getIdentifier(),
					'name' => $format->getName(),
				],
				'rating' => $rating,

				'breadcrumbs' => $breadcrumbs,

				'prevMonth' => $prevMonth,
				'thisMonth' => $thisMonth,
				'nextMonth' => $nextMonth,

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
