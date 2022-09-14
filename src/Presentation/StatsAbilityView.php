<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\StatsAbilityModel;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;

final class StatsAbilityView
{
	public function __construct(
		private StatsAbilityModel $statsAbilityModel,
		private IntlFormatterFactory $formatterFactory,
		private MonthControlFormatter $monthControlFormatter,
	) {}

	/**
	 * Get usage data to create a list of Pokémon who use a specific ability.
	 */
	public function getData() : ResponseInterface
	{
		$month = $this->statsAbilityModel->getMonth();
		$format = $this->statsAbilityModel->getFormat();
		$rating = $this->statsAbilityModel->getRating();

		$formatter = $this->formatterFactory->createFor(
			$this->statsAbilityModel->getLanguageId()
		);

		// Get the previous month and the next month.
		$dateModel = $this->statsAbilityModel->getDateModel();
		$prevMonth = $dateModel->getPrevMonth();
		$thisMonth = $dateModel->getThisMonth();
		$nextMonth = $dateModel->getNextMonth();
		$prevMonth = $this->monthControlFormatter->format($prevMonth, $formatter);
		$thisMonth = $this->monthControlFormatter->format($thisMonth, $formatter);
		$nextMonth = $this->monthControlFormatter->format($nextMonth, $formatter);

		// Get the Pokémon usage data.
		$pokemonData = $this->statsAbilityModel->getPokemon();
		$pokemons = [];
		foreach ($pokemonData as $pokemon) {
			$pokemons[] = [
				'icon' => $pokemon->getIcon(),
				'identifier' => $pokemon->getIdentifier(),
				'name' => $pokemon->getName(),
				'pokemonPercent' => $pokemon->getPokemonPercent(),
				'pokemonPercentText' => $formatter->formatPercent($pokemon->getPokemonPercent()),
				'abilityPercent' => $pokemon->getAbilityPercent(),
				'abilityPercentText' => $formatter->formatPercent($pokemon->getAbilityPercent()),
				'usagePercent' => $pokemon->getUsagePercent(),
				'usagePercentText' => $formatter->formatPercent($pokemon->getUsagePercent()),
				'usageChange' => $pokemon->getUsageChange(),
				'usageChangeText' => $formatter->formatChange($pokemon->getUsageChange()),
			];
		}

		// Navigation breadcrumbs.
		$formatIdentifier = $format->getIdentifier();
		$abilityName = $this->statsAbilityModel->getAbilityName()->getName();
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
			'text' => $abilityName,
		]];

		return new JsonResponse([
			'data' => [
				'title' => 'Porydex - Stats - ' . $thisMonth['name'] . ' '
					. $format->getName() . ' - ' . $abilityName,

				'format' => [
					'identifier' => $format->getIdentifier(),
					'name' => $format->getName(),
				],
				'rating' => $rating,

				'breadcrumbs' => $breadcrumbs,
				'prevMonth' => $prevMonth,
				'thisMonth' => $thisMonth,
				'nextMonth' => $nextMonth,
				'ratings' => $this->statsAbilityModel->getRatings(),

				'ability' => [
					'identifier' => $this->statsAbilityModel->getAbilityIdentifier(),
					'name' => $abilityName,
					'description' => $this->statsAbilityModel->getAbilityDescription()->getDescription(),
				],

				// The main data.
				'pokemons' => $pokemons,
			]
		]);
	}
}
