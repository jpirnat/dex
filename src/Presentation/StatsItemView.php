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
		$prevMonth = $dateModel->prevMonth;
		$thisMonth = $dateModel->thisMonth;
		$nextMonth = $dateModel->nextMonth;
		$prevMonth = $this->monthControlFormatter->format($prevMonth, $formatter);
		$thisMonth = $this->monthControlFormatter->format($thisMonth, $formatter);
		$nextMonth = $this->monthControlFormatter->format($nextMonth, $formatter);

		// Get the Pokémon usage data.
		$pokemonData = $this->statsItemModel->pokemon;
		$pokemons = [];
		foreach ($pokemonData as $pokemon) {
			$pokemons[] = [
				'icon' => $pokemon->icon,
				'identifier' => $pokemon->identifier,
				'name' => $pokemon->name,
				'pokemonPercent' => $pokemon->pokemonPercent,
				'pokemonPercentText' => $formatter->formatPercent($pokemon->pokemonPercent),
				'itemPercent' => $pokemon->itemPercent,
				'itemPercentText' => $formatter->formatPercent($pokemon->itemPercent),
				'usagePercent' => $pokemon->usagePercent,
				'usagePercentText' => $formatter->formatPercent($pokemon->usagePercent),
				'usageChange' => $pokemon->usageChange,
				'usageChangeText' => $formatter->formatChange($pokemon->usageChange),
				'baseSpeed' => $pokemon->baseSpeed,
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
			'text' => $item['name'],
		]];

		return new JsonResponse([
			'data' => [
				'title' => 'Porydex - Stats - ' . $thisMonth['name'] . ' '
					. $format->name . ' - ' . $item['name'],

				'format' => [
					'identifier' => $format->identifier,
					'name' => $format->name,
				],
				'rating' => $rating,

				'breadcrumbs' => $breadcrumbs,
				'prevMonth' => $prevMonth,
				'thisMonth' => $thisMonth,
				'nextMonth' => $nextMonth,
				'ratings' => $this->statsItemModel->ratings,

				'versionGroup' => [
					'identifier' => $versionGroup->identifier,
				],
				'item' => $item,
				'speedName' => $this->statsItemModel->speedName,
				'pokemons' => $pokemons,
			]
		]);
	}
}
