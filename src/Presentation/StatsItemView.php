<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\StatsItemModel;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;

final readonly class StatsItemView
{
	public function __construct(
		private StatsItemModel $statsItemModel,
		private IntlFormatterFactory $formatterFactory,
		private MonthControlFormatter $monthControlFormatter,
	) {}

	/**
	 * Get data for the stats item page.
	 */
	public function getData() : ResponseInterface
	{
		$month = $this->statsItemModel->month;
		$format = $this->statsItemModel->format;
		$rating = $this->statsItemModel->rating;
		$item = $this->statsItemModel->item;

		$versionGroup = $this->statsItemModel->versionGroup;

		$formatter = $this->formatterFactory->createFor(
			$this->statsItemModel->languageId
		);

		// Get the previous month and the next month.
		$dateModel = $this->statsItemModel->dateModel;
		$prevMonth = $dateModel->getPrevMonth();
		$thisMonth = $dateModel->getThisMonth();
		$nextMonth = $dateModel->getNextMonth();
		$prevMonth = $this->monthControlFormatter->format($prevMonth, $formatter);
		$thisMonth = $this->monthControlFormatter->format($thisMonth, $formatter);
		$nextMonth = $this->monthControlFormatter->format($nextMonth, $formatter);

		// Get the Pokémon usage data.
		$pokemonData = $this->statsItemModel->pokemon;
		$pokemons = [];
		foreach ($pokemonData as $pokemon) {
			$pokemons[] = [
				'icon' => $pokemon->getIcon(),
				'identifier' => $pokemon->getIdentifier(),
				'name' => $pokemon->getName(),
				'pokemonPercent' => $pokemon->getPokemonPercent(),
				'pokemonPercentText' => $formatter->formatPercent($pokemon->getPokemonPercent()),
				'itemPercent' => $pokemon->getItemPercent(),
				'itemPercentText' => $formatter->formatPercent($pokemon->getItemPercent()),
				'usagePercent' => $pokemon->getUsagePercent(),
				'usagePercentText' => $formatter->formatPercent($pokemon->getUsagePercent()),
				'usageChange' => $pokemon->getUsageChange(),
				'usageChangeText' => $formatter->formatChange($pokemon->getUsageChange()),
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
			'text' => $item['name'],
		]];

		return new JsonResponse([
			'data' => [
				'title' => 'Porydex - Stats - ' . $thisMonth['name'] . ' '
					. $format->getName() . ' - ' . $item['name'],

				'format' => [
					'identifier' => $format->getIdentifier(),
					'name' => $format->getName(),
				],
				'rating' => $rating,

				'breadcrumbs' => $breadcrumbs,
				'prevMonth' => $prevMonth,
				'thisMonth' => $thisMonth,
				'nextMonth' => $nextMonth,
				'ratings' => $this->statsItemModel->ratings,

				'versionGroup' => [
					'identifier' => $versionGroup->getIdentifier(),
				],
				'item' => $item,
				'speedName' => $this->statsItemModel->speedName,
				'pokemons' => $pokemons,
			]
		]);
	}
}
