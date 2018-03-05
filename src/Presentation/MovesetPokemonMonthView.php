<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\MovesetPokemonMonth\AbilityData;
use Jp\Dex\Application\Models\MovesetPokemonMonth\CounterData;
use Jp\Dex\Application\Models\MovesetPokemonMonth\ItemData;
use Jp\Dex\Application\Models\MovesetPokemonMonth\MoveData;
use Jp\Dex\Application\Models\MovesetPokemonMonth\MovesetPokemonMonthModel;
use Jp\Dex\Application\Models\MovesetPokemonMonth\SpreadData;
use Jp\Dex\Application\Models\MovesetPokemonMonth\TeammateData;
use Jp\Dex\Domain\Stats\StatId;
use Psr\Http\Message\ResponseInterface;
use Twig_Environment;
use Zend\Diactoros\Response;

class MovesetPokemonMonthView
{
	/** @var Twig_Environment $twig */
	private $twig;

	/** @var BaseView $baseView */
	private $baseView;

	/** @var MovesetPokemonMonthModel $movesetPokemonMonthModel */
	private $movesetPokemonMonthModel;

	/** @var IntlFormatterFactory $formatterFactory */
	private $formatterFactory;

	/**
	 * Constructor.
	 *
	 * @param Twig_Environment $twig
	 * @param BaseView $baseView
	 * @param MovesetPokemonMonthModel $movesetPokemonMonthModel
	 * @param IntlFormatterFactory $formatterFactory
	 */
	public function __construct(
		Twig_Environment $twig,
		BaseView $baseView,
		MovesetPokemonMonthModel $movesetPokemonMonthModel,
		IntlFormatterFactory $formatterFactory
	) {
		$this->twig = $twig;
		$this->baseView = $baseView;
		$this->movesetPokemonMonthModel = $movesetPokemonMonthModel;
		$this->formatterFactory = $formatterFactory;
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
		$year = $this->movesetPokemonMonthModel->getYear();
		$month = $this->movesetPokemonMonthModel->getMonth();
		$formatIdentifier = $this->movesetPokemonMonthModel->getFormatIdentifier();
		$rating = $this->movesetPokemonMonthModel->getRating();

		$formatter = $this->formatterFactory->createFor(
			$this->movesetPokemonMonthModel->getLanguageId()
		);

		// Get the previous month and the next month.
		$prevMonth = $this->movesetPokemonMonthModel->getDateModel()->getPrevMonth();
		$nextMonth = $this->movesetPokemonMonthModel->getDateModel()->getNextMonth();

		$pokemon = $this->movesetPokemonMonthModel->getPokemon();
		$movesetPokemon = $this->movesetPokemonMonthModel->getMovesetPokemon();
		$movesetRatedPokemon = $this->movesetPokemonMonthModel->getMovesetRatedPokemon();

		// Get miscellaneous Pokémon data.
		$pokemonModel = $this->movesetPokemonMonthModel->getPokemonModel();
		$pokemonName = $pokemonModel->getPokemonName();
		$model = $pokemonModel->getModel();

		// Get types.
		$typeIcons = [];
		foreach ($pokemonModel->getTypeIcons() as $slot => $typeIcon) {
			$typeIcons[$slot] = $typeIcon->getImage();
		}

		// Get base stats.
		$baseStats = [];
		foreach ($pokemonModel->getStatDatas() as $statData) {
			$baseStats[] = [
				'name' => $statData->getStatName(),
				'value' => $statData->getBaseStat(),
			];
		}

		// Get abilities and sort by percent.
		$abilityDatas = $this->movesetPokemonMonthModel->getAbilityDatas();
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
		$itemDatas = $this->movesetPokemonMonthModel->getItemDatas();
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
		$spreadDatas = $this->movesetPokemonMonthModel->getSpreadDatas();
		uasort(
			$spreadDatas,
			function (SpreadData $a, SpreadData $b) : int {
				return $b->getPercent() <=> $a->getPercent();
			}
		);

		// Compile all spread data into the right form..
		$spreads = [];
		$statIds = [
			'hp' =>  new StatId(StatId::HP),
			'atk' => new StatId(StatId::ATTACK),
			'def' => new StatId(StatId::DEFENSE),
			'spa' => new StatId(StatId::SPECIAL_ATTACK),
			'spd' => new StatId(StatId::SPECIAL_DEFENSE),
			'spe' => new StatId(StatId::SPEED),
		];
		foreach ($spreadDatas as $spreadData) {
			// Create nature array with nature name and stat modifiers.
			$natureModifiers = $spreadData->getNatureModifiers();
			$nature['name'] = $spreadData->getNatureName();
			foreach ($statIds as $statAbbr => $statId) {
				// No natures modify HP.
				if ($statId->value() === StatId::HP) {
					continue;
				}

				$natureModifier = $natureModifiers->get($statId)->getValue();
				if ($natureModifier > 1) {
					$natureModifierSign = '+';
				} elseif ($natureModifier < 1) {
					$natureModifierSign = '-';
				} else {
					$natureModifierSign = '';
				}

				$nature[$statAbbr] = $natureModifierSign;
			}

			// Create EVs array with each stat's EV.
			$evSpread = $spreadData->getEvSpread();
			$evs = [];
			foreach ($statIds as $statAbbr => $statId) {
				$evs[$statAbbr] = $evSpread->get($statId)->getValue();
			}

			// Create stats array with each calculated stat.
			$statSpread = $spreadData->getStatSpread();
			$stats = [];
			foreach ($statIds as $statAbbr => $statId) {
				$stats[$statAbbr] = $statSpread->get($statId)->getValue();
			}

			// Put it all together!
			$spreads[] = [
				'nature' => $nature,
				'evs' => $evs,
				'percent' => $formatter->formatPercent($spreadData->getPercent()),
				'stats' => $stats,
			];
		}

		// Get moves and sort by percent.
		$moveDatas = $this->movesetPokemonMonthModel->getMoveDatas();
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
		$teammateDatas = $this->movesetPokemonMonthModel->getTeammateDatas();
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
		$counterDatas = $this->movesetPokemonMonthModel->getCounterDatas();
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
		$breadcrumbs = [
			[
				'url' => '/stats',
				'text' => 'Stats',
			],
			[
				'url' => "/stats/$year/$month",
				'text' => 'Formats',
			],
			[
				'url' => "/stats/$year/$month/$formatIdentifier/$rating",
				'text' => 'Usage',
			],
			[
				'text' => $pokemonName->getName(),
			],
		];

		$content = $this->twig->render(
			'html/moveset-pokemon-month.twig',
			$this->baseView->getBaseVariables() + [
				// TODO: title - "Month Year Format Pokémon usage stats"?
				'breadcrumbs' => $breadcrumbs,

				// The month control's data.
				'showPrevMonthLink' => $this->movesetPokemonMonthModel->doesPrevMonthDataExist(),
				'prevYear' => $prevMonth->getYear(),
				'prevMonth' => $prevMonth->getMonth(),
				'prevMonthText' => $formatter->formatYearMonth($prevMonth),
				'showNextMonthLink' => $this->movesetPokemonMonthModel->doesNextMonthDataExist(),
				'nextYear' => $nextMonth->getYear(),
				'nextMonth' => $nextMonth->getMonth(),
				'nextMonthText' => $formatter->formatYearMonth($nextMonth),
				'formatIdentifier' => $formatIdentifier,
				'rating' => $rating,
				'pokemonIdentifier' => $pokemon->getIdentifier(),

				'year' => $year,
				'month' => $month,
				'pokemonName' => $pokemonName->getName(),
				'model' => $model->getImage(),
				'typeIcons' => $typeIcons,
				'baseStats' => $baseStats,
				'rawCount' =>$movesetPokemon->getRawCount(),
				'averageWeight' => $movesetRatedPokemon->getAverageWeight(),
				'viabilityCeiling' => $movesetPokemon->getViabilityCeiling(),

				// The main data.
				'abilities' => $abilities,
				'items' => $items,
				'spreads' => $spreads,
				'moves' => $moves,
				'teammates' => $teammates,
				'counters' => $counters,
			]
		);

		$response = new Response();
		$response->getBody()->write($content);
		return $response;
	}
}
