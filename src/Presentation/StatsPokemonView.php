<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\StatsPokemon\AbilityData;
use Jp\Dex\Application\Models\StatsPokemon\CounterData;
use Jp\Dex\Application\Models\StatsPokemon\ItemData;
use Jp\Dex\Application\Models\StatsPokemon\MoveData;
use Jp\Dex\Application\Models\StatsPokemon\SpreadData;
use Jp\Dex\Application\Models\StatsPokemon\StatsPokemonModel;
use Jp\Dex\Application\Models\StatsPokemon\TeammateData;
use Jp\Dex\Domain\Stats\StatId;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response\HtmlResponse;

class StatsPokemonView
{
	/** @var RendererInterface $renderer */
	private $renderer;

	/** @var BaseView $baseView */
	private $baseView;

	/** @var StatsPokemonModel $statsPokemonModel */
	private $statsPokemonModel;

	/** @var IntlFormatterFactory $formatterFactory */
	private $formatterFactory;

	/** @var DexFormatter $dexFormatter */
	private $dexFormatter;

	/**
	 * Constructor.
	 *
	 * @param RendererInterface $renderer
	 * @param BaseView $baseView
	 * @param StatsPokemonModel $statsPokemonModel
	 * @param IntlFormatterFactory $formatterFactory
	 * @param DexFormatter $dexFormatter
	 */
	public function __construct(
		RendererInterface $renderer,
		BaseView $baseView,
		StatsPokemonModel $statsPokemonModel,
		IntlFormatterFactory $formatterFactory,
		DexFormatter $dexFormatter
	) {
		$this->renderer = $renderer;
		$this->baseView = $baseView;
		$this->statsPokemonModel = $statsPokemonModel;
		$this->formatterFactory = $formatterFactory;
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

		// Get miscellaneous Pokémon data.
		$pokemonModel = $this->statsPokemonModel->getPokemonModel();
		$pokemonName = $pokemonModel->getPokemonName();
		$model = $pokemonModel->getModel();
		$generation = $this->statsPokemonModel->getGeneration();

		// Get base stats.
		$baseStats = [];
		foreach ($pokemonModel->getStatDatas() as $statData) {
			$baseStats[] = [
				'name' => $statData->getStatName(),
				'value' => $statData->getBaseStat(),
			];
		}

		// Get abilities and sort by percent.
		$abilityDatas = $this->statsPokemonModel->getAbilityDatas();
		uasort(
			$abilityDatas,
			function (AbilityData $a, AbilityData $b) : int {
				return $b->getPercent() <=> $a->getPercent();
			}
		);

		// Compile all ability data into the right form.
		$abilities = [];
		foreach ($abilityDatas as $abilityData) {
			$abilities[] = [
				'name' => $abilityData->getAbilityName(),
				'identifier' => $abilityData->getAbilityIdentifier(),
				'percent' => $formatter->formatPercent($abilityData->getPercent()),
				'change' => $abilityData->getChange(),
				'changeText' => $formatter->formatPercent($abilityData->getChange()),
			];
		}

		// Get items and sort by percent.
		$itemDatas = $this->statsPokemonModel->getItemDatas();
		uasort(
			$itemDatas,
			function (ItemData $a, ItemData $b) : int {
				return $b->getPercent() <=> $a->getPercent();
			}
		);

		// Compile all item data into the right form.
		$items = [];
		foreach ($itemDatas as $itemData) {
			$items[] = [
				'name' => $itemData->getItemName(),
				'identifier' => $itemData->getItemIdentifier(),
				'percent' => $formatter->formatPercent($itemData->getPercent()),
				'change' => $itemData->getChange(),
				'changeText' => $formatter->formatPercent($itemData->getChange()),
			];
		}

		// Get spreads and sort by percent.
		$spreadDatas = $this->statsPokemonModel->getSpreadDatas();
		uasort(
			$spreadDatas,
			function (SpreadData $a, SpreadData $b) : int {
				return $b->getPercent() <=> $a->getPercent();
			}
		);

		// Compile all spread data into the right form.
		$spreads = [];
		$statIds = StatId::getByGeneration($generation->getId());
		foreach ($spreadDatas as $spreadData) {
			// Create nature array with stat modifiers.
			$nature = [];
			foreach ($statIds as $i => $statId) {
				$nature[$i] = ''; // Default.
				if ($spreadData->getIncreasedStatId() === null) {
					continue;
				}
				if ($statId->value() === $spreadData->getIncreasedStatId()->value()) {
					$nature[$i] = '+';
				}
				if ($statId->value() === $spreadData->getDecreasedStatId()->value()) {
					$nature[$i] = '-';
				}
			}

			// Create EV spread string.
			$evSpread = $spreadData->getEvSpread();
			$evs = [];
			foreach ($statIds as $i => $statId) {
				$evs[] = $evSpread->get($statId)->getValue() . $nature[$i];
			}
			$evs = implode(' / ', $evs);

			// Create calculated stats string.
			$statSpread = $spreadData->getStatSpread();
			$stats = [];
			foreach ($statIds as $statId) {
				$stats[] = $statSpread->get($statId)->getValue();
			}
			$stats = implode(' / ', $stats);

			// Put it all together!
			$spreads[] = [
				'nature' => $spreadData->getNatureName(),
				'evs' => $evs,
				'percent' => $formatter->formatPercent($spreadData->getPercent()),
				'stats' => $stats,
			];
		}

		// Get moves and sort by percent.
		$moveDatas = $this->statsPokemonModel->getMoveDatas();
		uasort(
			$moveDatas,
			function (MoveData $a, MoveData $b) : int {
				return $b->getPercent() <=> $a->getPercent();
			}
		);

		// Compile all move data into the right form.
		$moves = [];
		foreach ($moveDatas as $moveData) {
			$moves[] = [
				'name' => $moveData->getMoveName(),
				'identifier' => $moveData->getMoveIdentifier(),
				'percent' => $formatter->formatPercent($moveData->getPercent()),
				'change' => $moveData->getChange(),
				'changeText' => $formatter->formatPercent($moveData->getChange()),
			];
		}

		// Get teammates and sort by percent.
		$teammateDatas = $this->statsPokemonModel->getTeammateDatas();
		uasort(
			$teammateDatas,
			function (TeammateData $a, TeammateData $b) : int {
				return $b->getPercent() <=> $a->getPercent();
			}
		);

		// Get teammate names.
		$teammates = [];
		foreach ($teammateDatas as $teammateData) {
			$teammates[] = [
				'name' => $teammateData->getPokemonName(),
				'showMovesetLink' => $teammateData->doesMovesetDataExist(),
				'identifier' => $teammateData->getPokemonIdentifier(),
				'formIcon' => $teammateData->getFormIcon(),
				'percent' => $formatter->formatPercent($teammateData->getPercent()),
			];
		}

		// Get counters and sort by percent.
		$counterDatas = $this->statsPokemonModel->getCounterDatas();
		uasort(
			$counterDatas,
			function (CounterData $a, CounterData $b) : int {
				return $b->getNumber1() <=> $a->getNumber1();
			}
		);

		// Compile all counter data into the right form.
		$counters = [];
		foreach ($counterDatas as $counterData) {
			$counters[] = [
				'name' => $counterData->getPokemonName(),
				'showMovesetLink' => $counterData->doesMovesetDataExist(),
				'identifier' => $counterData->getPokemonIdentifier(),
				'formIcon' => $counterData->getFormIcon(),
				'number1' => $formatter->formatNumber($counterData->getNumber1()),
				'number2' => $formatter->formatNumber($counterData->getNumber2()),
				'number3' => $formatter->formatNumber($counterData->getNumber3()),
				'percentKnockedOut' => $formatter->formatPercent($counterData->getPercentKnockedOut()),
				'percentSwitchedOut' => $formatter->formatPercent($counterData->getPercentSwitchedOut()),
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
				'text' => $pokemonName->getName(),
			],
		];

		$content = $this->renderer->render(
			'html/moveset-pokemon-month.twig',
			$this->baseView->getBaseVariables() + [
				'month' => $month,
				'format' => [
					'identifier' => $format->getIdentifier(),
					'smogonDexIdentifier' => $format->getSmogonDexIdentifier(),
				],
				'rating' => $rating,
				'pokemon' => [
					'identifier' => $pokemon->getIdentifier(),
					'name' => $pokemonName->getName(),
					'smogonDexIdentifier' => $pokemon->getSmogonDexIdentifier(),
				],

				'breadcrumbs' => $breadcrumbs,

				'prevMonth' => [
					'show' => $this->statsPokemonModel->doesPrevMonthDataExist(),
					'month' => $prevMonth->format('Y-m'),
					'text' => $formatter->formatMonth($prevMonth),
				],
				'nextMonth' => [
					'show' => $this->statsPokemonModel->doesNextMonthDataExist(),
					'month' => $nextMonth->format('Y-m'),
					'text' => $formatter->formatMonth($nextMonth),
				],
				'ratings' => $this->statsPokemonModel->getRatings(),

				'model' => $model->getImage(),
				'types' => $this->dexFormatter->formatDexTypes($pokemonModel->getTypes()),
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
