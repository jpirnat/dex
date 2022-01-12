<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\StatsUsageModel;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;

final class StatsUsageView
{
	public function __construct(
		private StatsUsageModel $statsUsageModel,
		private IntlFormatterFactory $formatterFactory,
		private MonthControlFormatter $monthControlFormatter,
	) {}

	/**
	 * Get usage data to recreate a stats usage file, such as
	 * http://www.smogon.com/stats/2014-11/ou-1695.txt.
	 */
	public function getData() : ResponseInterface
	{
		$month = $this->statsUsageModel->getMonth();
		$format = $this->statsUsageModel->getFormat();
		$rating = $this->statsUsageModel->getRating();

		$formatter = $this->formatterFactory->createFor(
			$this->statsUsageModel->getLanguageId()
		);

		// Get the previous month and the next month.
		$dateModel = $this->statsUsageModel->getDateModel();
		$prevMonth = $dateModel->getPrevMonth();
		$thisMonth = $dateModel->getThisMonth();
		$nextMonth = $dateModel->getNextMonth();
		$prevMonth = $this->monthControlFormatter->format($prevMonth, $formatter);
		$thisMonth = $this->monthControlFormatter->format($thisMonth, $formatter);
		$nextMonth = $this->monthControlFormatter->format($nextMonth, $formatter);

		// Get the Pokémon usage data.
		$pokemonData = $this->statsUsageModel->getPokemon();
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
			];
		}

		// Navigation breadcrumbs.
		$breadcrumbs = [[
			'url' => '/stats',
			'text' => 'Stats',
		], [
			'url' => "/stats/$month",
			'text' => $thisMonth['text'],
		], [
			'text' => $format->getName(),
		]];

		return new JsonResponse([
			'data' => [
				'title' => 'Porydex - Stats - ' . $thisMonth['text'] . ' '
					. $format->getName(),

				'format' => [
					'identifier' => $format->getIdentifier(),
					'name' => $format->getName(),
				],
				'rating' => $rating,

				'breadcrumbs' => $breadcrumbs,
				'prevMonth' => $prevMonth,
				'thisMonth' => $thisMonth,
				'nextMonth' => $nextMonth,
				'ratings' => $this->statsUsageModel->getRatings(),
				'showLeadsLink' => $this->statsUsageModel->doesLeadsDataExist(),

				// The main data.
				'pokemons' => $pokemons,

				'myFormat' => $this->statsUsageModel->getMyFormat(),
				'myRating' => $this->statsUsageModel->getMyRating(),
			]
		]);
	}
}
