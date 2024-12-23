<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\StatsPokemon\StatsPokemonModel;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;

final readonly class StatsPokemonView
{
	public function __construct(
		private StatsPokemonModel $statsPokemonModel,
		private IntlFormatterFactory $formatterFactory,
		private MonthControlFormatter $monthControlFormatter,
		private DexFormatter $dexFormatter,
	) {}

	/**
	 * Get data for the stats Pokémon page.
	 */
	public function getData() : ResponseInterface
	{
		$month = $this->statsPokemonModel->month;
		$format = $this->statsPokemonModel->format;
		$rating = $this->statsPokemonModel->rating;
		$pokemon = $this->statsPokemonModel->pokemon;

		$formatter = $this->formatterFactory->createFor(
			$this->statsPokemonModel->languageId
		);

		// Get the previous month and the next month.
		$dateModel = $this->statsPokemonModel->dateModel;
		$prevMonth = $dateModel->prevMonth;
		$thisMonth = $dateModel->thisMonth;
		$nextMonth = $dateModel->nextMonth;
		$prevMonth = $this->monthControlFormatter->format($prevMonth, $formatter);
		$thisMonth = $this->monthControlFormatter->format($thisMonth, $formatter);
		$nextMonth = $this->monthControlFormatter->format($nextMonth, $formatter);

		$months = [];
		$allMonths = $this->statsPokemonModel->months;
		foreach ($allMonths as $m) {
			$months[] = $this->monthControlFormatter->format($m, $formatter);
		}

		$prevRank = $this->statsPokemonModel->prevRank;
		$thisRank = $this->statsPokemonModel->thisRank;
		$nextRank = $this->statsPokemonModel->nextRank;

		$movesetPokemon = $this->statsPokemonModel->movesetPokemon;
		$movesetRatedPokemon = $this->statsPokemonModel->movesetRatedPokemon;

		$rawCount = $movesetPokemon?->getRawCount();
		$averageWeight = $movesetRatedPokemon?->getAverageWeight();
		$viabilityCeiling = $movesetPokemon?->getViabilityCeiling();

		// Get miscellaneous Pokémon data.
		$pokemonModel = $this->statsPokemonModel->pokemonModel;
		$dexPokemon = $pokemonModel->pokemon;
		$stats = $pokemonModel->stats;
		$versionGroup = $this->statsPokemonModel->versionGroup;
		$generation = $this->statsPokemonModel->generation;

		// Get abilities.
		$abilitiesData = $this->statsPokemonModel->abilities;
		$abilities = [];
		foreach ($abilitiesData as $ability) {
			$abilities[] = [
				'identifier' => $ability->identifier,
				'name' => $ability->name,
				'percent' => $ability->percent,
				'percentText' => $formatter->formatPercent($ability->percent),
				'change' => $ability->change,
				'changeText' => $formatter->formatChange($ability->change),
			];
		}

		// Get items.
		$itemsData = $this->statsPokemonModel->items;
		$items = [];
		foreach ($itemsData as $item) {
			$items[] = [
				'icon' => $item->getIcon(),
				'identifier' => $item->getIdentifier(),
				'name' => $item->getName(),
				'percent' => $item->getPercent(),
				'percentText' => $formatter->formatPercent($item->getPercent()),
				'change' => $item->getChange(),
				'changeText' => $formatter->formatChange($item->getChange()),
			];
		}

		// Get spreads and sort by percent.
		$spreadModel = $this->statsPokemonModel->spreadModel;
		$spreads = $spreadModel->spreads;
		foreach ($spreads as $i => $spread) {
			$percent = $formatter->formatPercent($spread['percent']);
			$spreads[$i]['percent'] = $percent;
		}

		// Get moves.
		$movesData = $this->statsPokemonModel->moves;
		$moves = [];
		foreach ($movesData as $move) {
			$moves[] = [
				'identifier' => $move->getIdentifier(),
				'name' => $move->getName(),
				'percent' => $move->getPercent(),
				'percentText' => $formatter->formatPercent($move->getPercent()),
				'change' => $move->getChange(),
				'changeText' => $formatter->formatChange($move->getChange()),
				'type' => $this->dexFormatter->formatDexType($move->getType()),
				'category' => $this->dexFormatter->formatDexCategory($move->getCategory()),
				'pp' => $move->getPP() * 1.6,
				'power' => $move->getPower(),
				'accuracy' => $move->getAccuracy(),
				'priority' => $move->getPriority(),
				'hitsMultiplePokemon' => $move->getTargetId()->hitsMultiplePokemon(),
			];
		}

		// Get Tera types.
		$teraTypesData = $this->statsPokemonModel->teraTypes;
		$teraTypes = [];
		foreach ($teraTypesData as $teraType) {
			$teraTypes[] = [
				'icon' => $teraType->getIcon(),
				'identifier' => $teraType->getIdentifier(),
				'name' => $teraType->getName(),
				'percent' => $teraType->getPercent(),
				'percentText' => $formatter->formatPercent($teraType->getPercent()),
				'change' => $teraType->getChange(),
				'changeText' => $formatter->formatChange($teraType->getChange()),
			];
		}

		// Get teammates.
		$teammatesData = $this->statsPokemonModel->teammates;
		$teammates = [];
		foreach ($teammatesData as $teammate) {
			$teammates[] = [
				'icon' => $teammate->getIcon(),
				'showMovesetLink' => true, // TODO
				'identifier' => $teammate->getIdentifier(),
				'name' => $teammate->getName(),
				'percent' => $teammate->getPercent(),
				'percentText' => $formatter->formatPercent($teammate->getPercent()),
			];
		}

		// Get counters.
		$countersData = $this->statsPokemonModel->counters;
		$counters = [];
		foreach ($countersData as $counter) {
			$counters[] = [
				'icon' => $counter->getIcon(),
				'showMovesetLink' => true, // TODO
				'identifier' => $counter->getIdentifier(),
				'name' => $counter->getName(),
				'score' => $formatter->formatNumber($counter->getScore()),
				'scoreText' => $formatter->formatNumber($counter->getScore()),
				'percent' => $counter->getPercent(),
				'percentText' => $formatter->formatPercent($counter->getPercent()),
				'standardDeviation' => $counter->getStandardDeviation(),
				'standardDeviationText' => $formatter->formatNumber($counter->getStandardDeviation()),
				'percentKnockedOut' => $counter->getPercentKnockedOut(),
				'percentKnockedOutText' => $formatter->formatPercent($counter->getPercentKnockedOut()),
				'percentSwitchedOut' => $counter->getPercentSwitchedOut(),
				'percentSwitchedOutText' => $formatter->formatPercent($counter->getPercentSwitchedOut()),
			];
		}

		// Navigation breadcrumbs.
		$formatIdentifier = $format->getIdentifier();
		$breadcrumbs = [[
			'url' => '/stats',
			'text' => 'Stats',
		], [
			'url' => "/stats/$month",
			'text' => $thisMonth['name'],
		], [
			'url' => "/stats/$month/$formatIdentifier/$rating",
			'text' => $format->getName(),
		], [
			'text' => $dexPokemon->getName(),
		]];

		return new JsonResponse([
			'data' => [
				'title' => 'Porydex - Stats - ' . $thisMonth['name'] . ' '
					. $format->getName() . ' - ' . $dexPokemon->getName(),

				'format' => [
					'identifier' => $format->getIdentifier(),
					'smogonDexIdentifier' => $format->getSmogonDexIdentifier(),
					'fieldSize' => $format->getFieldSize(),
				],
				'rating' => $rating,
				'pokemon' => [
					'identifier' => $dexPokemon->getIdentifier(),
					'name' => $dexPokemon->getName(),
					'sprite' => $dexPokemon->getSprite(),
					'types' => $this->dexFormatter->formatDexTypes($dexPokemon->getTypes()),
					'baseStats' => $dexPokemon->getBaseStats(),
					'bst' => $dexPokemon->getBst(),
					'smogonDexIdentifier' => $pokemon->getSmogonDexIdentifier(),
				],
				'stats' => $stats,

				'breadcrumbs' => $breadcrumbs,
				'prevMonth' => $prevMonth,
				'thisMonth' => $thisMonth,
				'nextMonth' => $nextMonth,
				'ratings' => $this->statsPokemonModel->ratings,
				'prevRank' => $prevRank,
				'thisRank' => $thisRank,
				'nextRank' => $nextRank,

				'versionGroup' => [
					'identifier' => $versionGroup->getIdentifier(),
				],
				'generation' => [
					'smogonDexIdentifier' => $generation->getSmogonDexIdentifier(),
				],
				'rawCount' => $rawCount,
				'averageWeight' => $averageWeight,
				'viabilityCeiling' => $viabilityCeiling,

				// The main data.
				'showAbilities' => $versionGroup->getId()->hasAbilities(),
				'showItems' => $versionGroup->getId()->hasHeldItems(),
				'showTeraTypes' => $versionGroup->getId()->hasTeraTypes(),
				'abilities' => $abilities,
				'items' => $items,
				'spreads' => $spreads,
				'moves' => $moves,
				'teraTypes' => $teraTypes,
				'teammates' => $teammates,
				'counters' => $counters,

				'months' => $months,
			]
		]);
	}
}
