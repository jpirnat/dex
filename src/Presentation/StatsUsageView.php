<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\StatsUsageModel;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;

final readonly class StatsUsageView
{
	public function __construct(
		private StatsUsageModel $statsUsageModel,
		private IntlFormatterFactory $formatterFactory,
		private MonthControlFormatter $monthControlFormatter,
	) {}

	/**
	 * Get data for the stats usage page.
	 */
	public function getData() : ResponseInterface
	{
		$month = $this->statsUsageModel->month;
		$format = $this->statsUsageModel->format;
		$rating = $this->statsUsageModel->rating;

		$formatter = $this->formatterFactory->createFor(
			$this->statsUsageModel->languageId
		);

		// Get the previous month and the next month.
		$dateModel = $this->statsUsageModel->dateModel;
		$prevMonth = $dateModel->prevMonth;
		$thisMonth = $dateModel->thisMonth;
		$nextMonth = $dateModel->nextMonth;
		$prevMonth = $this->monthControlFormatter->format($prevMonth, $formatter);
		$thisMonth = $this->monthControlFormatter->format($thisMonth, $formatter);
		$nextMonth = $this->monthControlFormatter->format($nextMonth, $formatter);

		$months = [];
		$allMonths = $this->statsUsageModel->months;
		foreach ($allMonths as $m) {
			$months[] = $this->monthControlFormatter->format($m, $formatter);
		}

		// Get the Pokémon usage data.
		$pokemonData = $this->statsUsageModel->pokemon;
		$pokemons = [];
		foreach ($pokemonData as $pokemon) {
			$pokemons[] = [
				'rank' => $pokemon->getRank(),
				'icon' => $pokemon->getIcon(),
				'identifier' => $pokemon->getIdentifier(),
				'name' => $pokemon->getName(),
				'usagePercent' => $pokemon->getUsagePercent(),
				'usagePercentText' => $formatter->formatPercent($pokemon->getUsagePercent()),
				'usageChange' => $pokemon->getUsageChange(),
				'usageChangeText' => $formatter->formatChange($pokemon->getUsageChange()),
				'raw' => $pokemon->getRaw(),
				'rawText' => $formatter->formatNumber($pokemon->getRaw()),
				'rawPercent' => $pokemon->getRawPercent(),
				'rawPercentText' => $formatter->formatPercent($pokemon->getRawPercent()),
				'real' => $pokemon->getReal(),
				'realText' => $formatter->formatNumber($pokemon->getReal()),
				'realPercent' => $pokemon->getRealPercent(),
				'realPercentText' => $formatter->formatPercent($pokemon->getRealPercent()),
				'baseSpeed' => $pokemon->getBaseSpeed(),
			];
		}

		// Navigation breadcrumbs.
		$breadcrumbs = [[
			'url' => '/stats',
			'text' => 'Stats',
		], [
			'url' => "/stats/$month",
			'text' => $thisMonth['name'],
		], [
			'text' => $format->name,
		]];

		return new JsonResponse([
			'data' => [
				'title' => 'Porydex - Stats - ' . $thisMonth['name'] . ' '
					. $format->name,
				'format' => [
					'identifier' => $format->identifier,
					'name' => $format->name,
				],
				'rating' => $rating,
				'breadcrumbs' => $breadcrumbs,
				'prevMonth' => $prevMonth,
				'thisMonth' => $thisMonth,
				'nextMonth' => $nextMonth,
				'ratings' => $this->statsUsageModel->ratings,
				'showLeadsLink' => $this->statsUsageModel->showLeadsLink,
				'myFormat' => $this->statsUsageModel->myFormat,
				'myRating' => $this->statsUsageModel->myRating,
				'speedName' => $this->statsUsageModel->speedName,
				'pokemons' => $pokemons,
				'months' => $months,
			]
		]);
	}
}
