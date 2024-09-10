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
		$month = $this->statsPokemonModel->getMonth();
		$format = $this->statsPokemonModel->getFormat();
		$rating = $this->statsPokemonModel->getRating();
		$pokemon = $this->statsPokemonModel->getPokemon();

		$formatter = $this->formatterFactory->createFor(
			$this->statsPokemonModel->getLanguageId()
		);

		// Get the previous month and the next month.
		$dateModel = $this->statsPokemonModel->getDateModel();
		$prevMonth = $dateModel->getPrevMonth();
		$thisMonth = $dateModel->getThisMonth();
		$nextMonth = $dateModel->getNextMonth();
		$prevMonth = $this->monthControlFormatter->format($prevMonth, $formatter);
		$thisMonth = $this->monthControlFormatter->format($thisMonth, $formatter);
		$nextMonth = $this->monthControlFormatter->format($nextMonth, $formatter);

		$months = [];
		$allMonths = $this->statsPokemonModel->getMonths();
		foreach ($allMonths as $m) {
			$months[] = $this->monthControlFormatter->format($m, $formatter);
		}

		$prevRank = $this->statsPokemonModel->getPrevRank();
		$thisRank = $this->statsPokemonModel->getThisRank();
		$nextRank = $this->statsPokemonModel->getNextRank();

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
		$dexPokemon = $pokemonModel->getPokemon();
		$baseStats = $pokemonModel->getBaseStats();
		$versionGroup = $this->statsPokemonModel->getVersionGroup();
		$generation = $this->statsPokemonModel->getGeneration();

		// Get abilities.
		$abilitiesData = $this->statsPokemonModel->getAbilities();
		$abilities = [];
		foreach ($abilitiesData as $ability) {
			$abilities[] = [
				'identifier' => $ability->getIdentifier(),
				'name' => $ability->getName(),
				'percent' => $ability->getPercent(),
				'percentText' => $formatter->formatPercent($ability->getPercent()),
				'change' => $ability->getChange(),
				'changeText' => $formatter->formatChange($ability->getChange()),
			];
		}

		// Get items.
		$itemsData = $this->statsPokemonModel->getItems();
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
		$spreadModel = $this->statsPokemonModel->getSpreadModel();
		$stats = $spreadModel->getStats();
		$spreads = $spreadModel->getSpreads();
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
				'percent' => $teammate->getPercent(),
				'percentText' => $formatter->formatPercent($teammate->getPercent()),
			];
		}

		// Get Tera types.
		$teraTypesData = $this->statsPokemonModel->getTeraTypes();
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
				],
				'rating' => $rating,
				'pokemon' => [
					'identifier' => $dexPokemon->getIdentifier(),
					'name' => $dexPokemon->getName(),
					'image' => $pokemonModel->getImage(),
					'types' => $this->dexFormatter->formatDexTypes($dexPokemon->getTypes()),
					'baseStats' => $baseStats,
					'smogonDexIdentifier' => $pokemon->getSmogonDexIdentifier(),
				],

				'breadcrumbs' => $breadcrumbs,
				'prevMonth' => $prevMonth,
				'thisMonth' => $thisMonth,
				'nextMonth' => $nextMonth,
				'ratings' => $this->statsPokemonModel->getRatings(),
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
				'stats' => $stats,
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
