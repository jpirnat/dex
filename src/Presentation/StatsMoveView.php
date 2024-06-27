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
		$month = $this->statsMoveModel->getMonth();
		$format = $this->statsMoveModel->getFormat();
		$rating = $this->statsMoveModel->getRating();

		$formatter = $this->formatterFactory->createFor(
			$this->statsMoveModel->getLanguageId()
		);

		// Get the previous month and the next month.
		$dateModel = $this->statsMoveModel->getDateModel();
		$prevMonth = $dateModel->getPrevMonth();
		$thisMonth = $dateModel->getThisMonth();
		$nextMonth = $dateModel->getNextMonth();
		$prevMonth = $this->monthControlFormatter->format($prevMonth, $formatter);
		$thisMonth = $this->monthControlFormatter->format($thisMonth, $formatter);
		$nextMonth = $this->monthControlFormatter->format($nextMonth, $formatter);

		// Get the Pokémon usage data.
		$pokemonData = $this->statsMoveModel->getPokemon();
		$pokemons = [];
		foreach ($pokemonData as $pokemon) {
			$pokemons[] = [
				'icon' => $pokemon->getIcon(),
				'identifier' => $pokemon->getIdentifier(),
				'name' => $pokemon->getName(),
				'pokemonPercent' => $pokemon->getPokemonPercent(),
				'pokemonPercentText' => $formatter->formatPercent($pokemon->getPokemonPercent()),
				'movePercent' => $pokemon->getMovePercent(),
				'movePercentText' => $formatter->formatPercent($pokemon->getMovePercent()),
				'usagePercent' => $pokemon->getUsagePercent(),
				'usagePercentText' => $formatter->formatPercent($pokemon->getUsagePercent()),
				'usageChange' => $pokemon->getUsageChange(),
				'usageChangeText' => $formatter->formatChange($pokemon->getUsageChange()),
			];
		}

		// Navigation breadcrumbs.
		$formatIdentifier = $format->getIdentifier();
		$moveName = $this->statsMoveModel->getMoveName()->getName();
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
			'text' => $moveName,
		]];

		return new JsonResponse([
			'data' => [
				'title' => 'Porydex - Stats - ' . $thisMonth['name'] . ' '
					. $format->getName() . ' - ' . $moveName,

				'format' => [
					'identifier' => $format->getIdentifier(),
					'name' => $format->getName(),
				],
				'rating' => $rating,

				'breadcrumbs' => $breadcrumbs,
				'prevMonth' => $prevMonth,
				'thisMonth' => $thisMonth,
				'nextMonth' => $nextMonth,
				'ratings' => $this->statsMoveModel->getRatings(),

				'move' => [
					'identifier' => $this->statsMoveModel->getMoveIdentifier(),
					'name' => $moveName,
					'description' => $this->statsMoveModel->getMoveDescription()->getDescription(),
				],

				// The main data.
				'pokemons' => $pokemons,
			]
		]);
	}
}
