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

		$rawCount = $movesetPokemon?->rawCount;
		$averageWeight = $movesetRatedPokemon?->averageWeight;
		$viabilityCeiling = $movesetPokemon?->viabilityCeiling;

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
				'icon' => $item->icon,
				'identifier' => $item->identifier,
				'name' => $item->name,
				'percent' => $item->percent,
				'percentText' => $formatter->formatPercent($item->percent),
				'change' => $item->change,
				'changeText' => $formatter->formatChange($item->change),
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
				'identifier' => $move->identifier,
				'name' => $move->name,
				'percent' => $move->percent,
				'percentText' => $formatter->formatPercent($move->percent),
				'change' => $move->change,
				'changeText' => $formatter->formatChange($move->change),
				'type' => $this->dexFormatter->formatDexType($move->type),
				'category' => $this->dexFormatter->formatDexCategory($move->category),
				'pp' => $move->pp * 1.6,
				'power' => $move->power,
				'accuracy' => $move->accuracy,
				'priority' => $move->priority,
				'hitsMultiplePokemon' => $move->targetId->hitsMultiplePokemon(),
			];
		}

		// Get Tera types.
		$teraTypesData = $this->statsPokemonModel->teraTypes;
		$teraTypes = [];
		foreach ($teraTypesData as $teraType) {
			$teraTypes[] = [
				'icon' => $teraType->icon,
				'identifier' => $teraType->identifier,
				'name' => $teraType->name,
				'percent' => $teraType->percent,
				'percentText' => $formatter->formatPercent($teraType->percent),
				'change' => $teraType->change,
				'changeText' => $formatter->formatChange($teraType->change),
			];
		}

		// Get teammates.
		$teammatesData = $this->statsPokemonModel->teammates;
		$teammates = [];
		foreach ($teammatesData as $teammate) {
			$teammates[] = [
				'icon' => $teammate->icon,
				'showMovesetLink' => true, // TODO
				'identifier' => $teammate->identifier,
				'name' => $teammate->name,
				'percent' => $teammate->percent,
				'percentText' => $formatter->formatPercent($teammate->percent),
			];
		}

		// Get counters.
		$countersData = $this->statsPokemonModel->counters;
		$counters = [];
		foreach ($countersData as $counter) {
			$counters[] = [
				'icon' => $counter->icon,
				'showMovesetLink' => true, // TODO
				'identifier' => $counter->identifier,
				'name' => $counter->name,
				'score' => $counter->score,
				'scoreText' => $formatter->formatNumber($counter->score),
				'percent' => $counter->percent,
				'percentText' => $formatter->formatPercent($counter->percent),
				'standardDeviation' => $counter->standardDeviation,
				'standardDeviationText' => $formatter->formatNumber($counter->standardDeviation),
				'percentKnockedOut' => $counter->percentKnockedOut,
				'percentKnockedOutText' => $formatter->formatPercent($counter->percentKnockedOut),
				'percentSwitchedOut' => $counter->percentSwitchedOut,
				'percentSwitchedOutText' => $formatter->formatPercent($counter->percentSwitchedOut),
			];
		}

		// Navigation breadcrumbs.
		$formatIdentifier = $format->identifier;
		$breadcrumbs = [[
			'url' => '/stats',
			'text' => 'Stats',
		], [
			'url' => "/stats/$month",
			'text' => $thisMonth['name'],
		], [
			'url' => "/stats/$month/$formatIdentifier/$rating",
			'text' => $format->name,
		], [
			'text' => $dexPokemon->name,
		]];

		return new JsonResponse([
			'data' => [
				'title' => 'Porydex - Stats - ' . $thisMonth['name'] . ' '
					. $format->name . ' - ' . $dexPokemon->name,

				'format' => [
					'identifier' => $format->identifier,
					'smogonDexIdentifier' => $format->smogonDexIdentifier,
					'fieldSize' => $format->fieldSize,
				],
				'rating' => $rating,
				'pokemon' => [
					'identifier' => $dexPokemon->identifier,
					'name' => $dexPokemon->name,
					'sprite' => $dexPokemon->sprite,
					'types' => $this->dexFormatter->formatDexTypes($dexPokemon->types),
					'baseStats' => $dexPokemon->baseStats,
					'bst' => $dexPokemon->bst,
					'smogonDexIdentifier' => $pokemon->smogonDexIdentifier,
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
					'identifier' => $versionGroup->identifier,
				],
				'generation' => [
					'smogonDexIdentifier' => $generation->smogonDexIdentifier,
				],
				'rawCount' => $rawCount,
				'averageWeight' => $averageWeight,
				'viabilityCeiling' => $viabilityCeiling,

				// The main data.
				'showAbilities' => $versionGroup->hasAbilities,
				'showItems' => $versionGroup->id->hasHeldItems(),
				'showTeraTypes' => $versionGroup->id->hasTeraTypes(),
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
