<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\StatsMoveModel;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;

final readonly class StatsMoveView
{
	public function __construct(
		private StatsMoveModel $statsMoveModel,
		private IntlFormatterFactory $formatterFactory,
		private MonthControlFormatter $monthControlFormatter,
	) {}

	/**
	 * Get data for the stats move page.
	 */
	public function getData() : ResponseInterface
	{
		$month = $this->statsMoveModel->month;
		$format = $this->statsMoveModel->format;
		$rating = $this->statsMoveModel->rating;
		$move = $this->statsMoveModel->move;

		$versionGroup = $this->statsMoveModel->versionGroup;

		$formatter = $this->formatterFactory->createFor(
			$this->statsMoveModel->languageId
		);

		// Get the previous month and the next month.
		$dateModel = $this->statsMoveModel->dateModel;
		$prevMonth = $dateModel->prevMonth;
		$thisMonth = $dateModel->thisMonth;
		$nextMonth = $dateModel->nextMonth;
		$prevMonth = $this->monthControlFormatter->format($prevMonth, $formatter);
		$thisMonth = $this->monthControlFormatter->format($thisMonth, $formatter);
		$nextMonth = $this->monthControlFormatter->format($nextMonth, $formatter);

		// Get the Pokémon usage data.
		$pokemonData = $this->statsMoveModel->pokemon;
		$pokemons = [];
		foreach ($pokemonData as $pokemon) {
			$pokemons[] = [
				'icon' => $pokemon->icon,
				'identifier' => $pokemon->identifier,
				'name' => $pokemon->name,
				'pokemonPercent' => $pokemon->pokemonPercent,
				'pokemonPercentText' => $formatter->formatPercent($pokemon->pokemonPercent),
				'movePercent' => $pokemon->movePercent,
				'movePercentText' => $formatter->formatPercent($pokemon->movePercent),
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
			'text' => $move['name'],
		]];

		return new JsonResponse([
			'data' => [
				'title' => 'Porydex - Stats - ' . $thisMonth['name'] . ' '
					. $format->name . ' - ' . $move['name'],

				'format' => [
					'identifier' => $format->identifier,
					'name' => $format->name,
				],
				'rating' => $rating,

				'breadcrumbs' => $breadcrumbs,
				'prevMonth' => $prevMonth,
				'thisMonth' => $thisMonth,
				'nextMonth' => $nextMonth,
				'ratings' => $this->statsMoveModel->ratings,

				'versionGroup' => [
					'identifier' => $versionGroup->identifier,
				],
				'move' => [
					'identifier' => $move['identifier'],
					'name' => $move['name'],
					'description' => $move['description'],
				],
				'speedName' => $this->statsMoveModel->speedName,
				'pokemons' => $pokemons,
			]
		]);
	}
}
