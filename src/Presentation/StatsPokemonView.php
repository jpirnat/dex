<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\StatsPokemon\StatsPokemonModel;
use Laminas\Diactoros\Response\HtmlResponse;
use Psr\Http\Message\ResponseInterface;

final class StatsPokemonView
{
	private RendererInterface $renderer;
	private BaseView $baseView;
	private StatsPokemonModel $statsPokemonModel;
	private IntlFormatterFactory $formatterFactory;
	private MonthControlFormatter $monthControlFormatter;
	private DexFormatter $dexFormatter;

	/**
	 * Constructor.
	 *
	 * @param RendererInterface $renderer
	 * @param BaseView $baseView
	 * @param StatsPokemonModel $statsPokemonModel
	 * @param IntlFormatterFactory $formatterFactory
	 * @param MonthControlFormatter $monthControlFormatter
	 * @param DexFormatter $dexFormatter
	 */
	public function __construct(
		RendererInterface $renderer,
		BaseView $baseView,
		StatsPokemonModel $statsPokemonModel,
		IntlFormatterFactory $formatterFactory,
		MonthControlFormatter $monthControlFormatter,
		DexFormatter $dexFormatter
	) {
		$this->renderer = $renderer;
		$this->baseView = $baseView;
		$this->statsPokemonModel = $statsPokemonModel;
		$this->formatterFactory = $formatterFactory;
		$this->monthControlFormatter = $monthControlFormatter;
		$this->dexFormatter = $dexFormatter;
	}

	/**
	 * Get moveset data to recreate a stats moveset file, such as
	 * http://www.smogon.com/stats/2014-11/moveset/ou-1695.txt, for a single
	 * Pokémon.
	 *
	 * @return ResponseInterface
	 */
	public function getData() : ResponseInterface
	{
		$month = $this->statsPokemonModel->getMonth();
		$format = $this->statsPokemonModel->getFormat();
		$rating = $this->statsPokemonModel->getRating();
		$pokemon = $this->statsPokemonModel->getPokemon();

		$formatter = $this->formatterFactory->createFor(
			$this->statsPokemonModel->getLanguageId()
		);

		// Get the previous month and the next month.
		$prevMonth = $this->statsPokemonModel->getDateModel()->getPrevMonth();
		$nextMonth = $this->statsPokemonModel->getDateModel()->getNextMonth();

		$movesetPokemon = $this->statsPokemonModel->getMovesetPokemon();
		$movesetRatedPokemon = $this->statsPokemonModel->getMovesetRatedPokemon();

		$rawCount = $movesetPokemon !== null
			? $movesetPokemon->getRawCount()
			: null;
		$averageWeight = $movesetRatedPokemon !== null
			? $movesetRatedPokemon->getAverageWeight()
			: null;
		$viabilityCeiling = $movesetPokemon !== null
			? $movesetPokemon->getViabilityCeiling()
			: null;

		$stats = $this->statsPokemonModel->getStats();

		// Get miscellaneous Pokémon data.
		$pokemonModel = $this->statsPokemonModel->getPokemonModel();
		$dexPokemon = $pokemonModel->getPokemon();
		$model = $pokemonModel->getModel();
		$baseStats = $dexPokemon->getBaseStats();
		$generation = $this->statsPokemonModel->getGeneration();

		// Get abilities.
		$abilitiesData = $this->statsPokemonModel->getAbilities();
		$abilities = [];
		foreach ($abilitiesData as $ability) {
			$abilities[] = [
				'identifier' => $ability->getIdentifier(),
				'name' => $ability->getName(),
				'percent' => $formatter->formatPercent($ability->getPercent()),
				'change' => $ability->getChange(),
				'changeText' => $formatter->formatChange($ability->getChange()),
			];
		}

		// Get items.
		$itemsData = $this->statsPokemonModel->getItems();
		$items = [];
		foreach ($itemsData as $item) {
			$items[] = [
				'identifier' => $item->getIdentifier(),
				'name' => $item->getName(),
				'percent' => $formatter->formatPercent($item->getPercent()),
				'change' => $item->getChange(),
				'changeText' => $formatter->formatChange($item->getChange()),
			];
		}

		// Get spreads and sort by percent.
		$spreads = $this->statsPokemonModel->getSpreads();
		foreach ($spreads as $i => $spread) {
			$percent = $formatter->formatPercent($spread['percent']);
			$spreads[$i]['percent'] = $percent;
		}

		// Get moves.
		$movesData = $this->statsPokemonModel->getMoves();
		$moves = [];
		foreach ($movesData as $move) {
			$moves[] = [
				'identifier' => $move->getIdentifier(),
				'name' => $move->getName(),
				'percent' => $formatter->formatPercent($move->getPercent()),
				'change' => $move->getChange(),
				'changeText' => $formatter->formatChange($move->getChange()),
			];
		}

		// Get teammates.
		$teammatesData = $this->statsPokemonModel->getTeammates();
		$teammates = [];
		foreach ($teammatesData as $teammate) {
			$teammates[] = [
				'icon' => $teammate->getIcon(),
				'showMovesetLink' => true, // TODO
				'identifier' => $teammate->getIdentifier(),
				'name' => $teammate->getName(),
				'percent' => $formatter->formatPercent($teammate->getPercent()),
			];
		}

		// Get counters.
		$countersData = $this->statsPokemonModel->getCounters();
		$counters = [];
		foreach ($countersData as $counter) {
			$counters[] = [
				'icon' => $counter->getIcon(),
				'showMovesetLink' => true, // TODO
				'identifier' => $counter->getIdentifier(),
				'name' => $counter->getName(),
				'score' => $formatter->formatNumber($counter->getScore()),
				'percent' => $formatter->formatPercent($counter->getPercent()),
				'standardDeviation' => $formatter->formatNumber($counter->getStandardDeviation()),
				'percentKnockedOut' => $formatter->formatPercent($counter->getPercentKnockedOut()),
				'percentSwitchedOut' => $formatter->formatPercent($counter->getPercentSwitchedOut()),
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
				'text' => $dexPokemon->getName(),
			],
		];

		$content = $this->renderer->render(
			'html/stats/pokemon.twig',
			$this->baseView->getBaseVariables() + [
				'month' => $month,
				'format' => [
					'identifier' => $format->getIdentifier(),
					'smogonDexIdentifier' => $format->getSmogonDexIdentifier(),
				],
				'rating' => $rating,
				'pokemon' => [
					'identifier' => $dexPokemon->getIdentifier(),
					'name' => $dexPokemon->getName(),
					'smogonDexIdentifier' => $pokemon->getSmogonDexIdentifier(),
				],

				'breadcrumbs' => $breadcrumbs,

				'prevMonth' => $this->monthControlFormatter->format($prevMonth, $formatter),
				'nextMonth' => $this->monthControlFormatter->format($nextMonth, $formatter),
				'ratings' => $this->statsPokemonModel->getRatings(),

				'stats' => $stats,
				'model' => $model->getImage(),
				'types' => $this->dexFormatter->formatDexTypes($dexPokemon->getTypes()),
				'baseStats' => $baseStats,
				'generation' => [
					'identifier' => $generation->getIdentifier(),
				],
				'rawCount' =>$rawCount,
				'averageWeight' => $averageWeight,
				'viabilityCeiling' => $viabilityCeiling,

				// The main data.
				'showAbilities' => $generation->getId()->value() >= 3,
				'showItems' => $generation->getId()->value() >= 2,
				'abilities' => $abilities,
				'items' => $items,
				'spreads' => $spreads,
				'moves' => $moves,
				'teammates' => $teammates,
				'counters' => $counters,
			]
		);

		return new HtmlResponse($content);
	}
}
