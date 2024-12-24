<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\StatsAbilityModel;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;

final readonly class StatsAbilityView
{
	public function __construct(
		private StatsAbilityModel $statsAbilityModel,
		private IntlFormatterFactory $formatterFactory,
		private MonthControlFormatter $monthControlFormatter,
	) {}

	/**
	 * Get data for the stats ability page.
	 */
	public function getData() : ResponseInterface
	{
		$month = $this->statsAbilityModel->month;
		$format = $this->statsAbilityModel->format;
		$rating = $this->statsAbilityModel->rating;
		$ability = $this->statsAbilityModel->ability;

		$versionGroup = $this->statsAbilityModel->versionGroup;

		$formatter = $this->formatterFactory->createFor(
			$this->statsAbilityModel->languageId
		);

		// Get the previous month and the next month.
		$dateModel = $this->statsAbilityModel->dateModel;
		$prevMonth = $dateModel->prevMonth;
		$thisMonth = $dateModel->thisMonth;
		$nextMonth = $dateModel->nextMonth;
		$prevMonth = $this->monthControlFormatter->format($prevMonth, $formatter);
		$thisMonth = $this->monthControlFormatter->format($thisMonth, $formatter);
		$nextMonth = $this->monthControlFormatter->format($nextMonth, $formatter);

		// Get the Pokémon usage data.
		$pokemonData = $this->statsAbilityModel->pokemon;
		$pokemons = [];
		foreach ($pokemonData as $pokemon) {
			$pokemons[] = [
				'icon' => $pokemon->icon,
				'identifier' => $pokemon->identifier,
				'name' => $pokemon->name,
				'pokemonPercent' => $pokemon->pokemonPercent,
				'pokemonPercentText' => $formatter->formatPercent($pokemon->pokemonPercent),
				'abilityPercent' => $pokemon->abilityPercent,
				'abilityPercentText' => $formatter->formatPercent($pokemon->abilityPercent),
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
			'text' => $ability['name'],
		]];

		return new JsonResponse([
			'data' => [
				'title' => 'Porydex - Stats - ' . $thisMonth['name'] . ' '
					. $format->name . ' - ' . $ability['name'],

				'format' => [
					'identifier' => $format->identifier,
					'name' => $format->name,
				],
				'rating' => $rating,

				'breadcrumbs' => $breadcrumbs,
				'prevMonth' => $prevMonth,
				'thisMonth' => $thisMonth,
				'nextMonth' => $nextMonth,
				'ratings' => $this->statsAbilityModel->ratings,

				'versionGroup' => [
					'identifier' => $versionGroup->identifier,
				],
				'ability' => [
					'identifier' => $ability['identifier'],
					'name' => $ability['name'],
					'description' => $ability['description'],
				],
				'speedName' => $this->statsAbilityModel->speedName,
				'pokemons' => $pokemons,
			]
		]);
	}
}
