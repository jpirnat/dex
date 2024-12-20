<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\StatsLeadsModel;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;

final readonly class StatsLeadsView
{
	public function __construct(
		private StatsLeadsModel $statsLeadsModel,
		private IntlFormatterFactory $formatterFactory,
		private MonthControlFormatter $monthControlFormatter,
	) {}

	/**
	 * Set data for the stats leads page.
	 */
	public function getData() : ResponseInterface
	{
		$month = $this->statsLeadsModel->month;
		$format = $this->statsLeadsModel->format;
		$rating = $this->statsLeadsModel->rating;

		$formatter = $this->formatterFactory->createFor(
			$this->statsLeadsModel->languageId
		);

		// Get the previous month and the next month.
		$dateModel = $this->statsLeadsModel->dateModel;
		$prevMonth = $dateModel->prevMonth;
		$thisMonth = $dateModel->thisMonth;
		$nextMonth = $dateModel->nextMonth;
		$prevMonth = $this->monthControlFormatter->format($prevMonth, $formatter);
		$thisMonth = $this->monthControlFormatter->format($thisMonth, $formatter);
		$nextMonth = $this->monthControlFormatter->format($nextMonth, $formatter);

		$months = [];
		$allMonths = $this->statsLeadsModel->months;
		foreach ($allMonths as $m) {
			$months[] = $this->monthControlFormatter->format($m, $formatter);
		}

		// Get the Pokémon usage data.
		$pokemonData = $this->statsLeadsModel->pokemon;
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
				'baseSpeed' => $pokemon->getBaseSpeed(),
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
			'text' => 'Leads',
		]];

		return new JsonResponse([
			'data' => [
				'title' => 'Porydex - Stats - ' . $thisMonth['name'] . ' '
					. $format->getName() . ' - Leads',

				'format' => [
					'identifier' => $format->getIdentifier(),
					'name' => $format->getName(),
				],
				'rating' => $rating,
				'breadcrumbs' => $breadcrumbs,
				'prevMonth' => $prevMonth,
				'thisMonth' => $thisMonth,
				'nextMonth' => $nextMonth,
				'ratings' => $this->statsLeadsModel->ratings,
				'speedName' => $this->statsLeadsModel->speedName,
				'pokemons' => $pokemons,
				'months' => $months,
			]
		]);
	}
}
