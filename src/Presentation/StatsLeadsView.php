<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\StatsLeadsModel;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;

final class StatsLeadsView
{
	private StatsLeadsModel $statsLeadsModel;
	private IntlFormatterFactory $formatterFactory;
	private MonthControlFormatter $monthControlFormatter;

	/**
	 * Constructor.
	 *
	 * @param StatsLeadsModel $statsLeadsModel
	 * @param IntlFormatterFactory $formatterFactory
	 * @param MonthControlFormatter $monthControlFormatter
	 */
	public function __construct(
		StatsLeadsModel $statsLeadsModel,
		IntlFormatterFactory $formatterFactory,
		MonthControlFormatter $monthControlFormatter
	) {
		$this->statsLeadsModel = $statsLeadsModel;
		$this->formatterFactory = $formatterFactory;
		$this->monthControlFormatter = $monthControlFormatter;
	}

	/**
	 * Get leads data to recreate a stats leads file, such as
	 * http://www.smogon.com/stats/leads/2014-11/ou-1695.txt.
	 *
	 * @return ResponseInterface
	 */
	public function getData() : ResponseInterface
	{
		$month = $this->statsLeadsModel->getMonth();
		$format = $this->statsLeadsModel->getFormat();
		$rating = $this->statsLeadsModel->getRating();

		$formatter = $this->formatterFactory->createFor(
			$this->statsLeadsModel->getLanguageId()
		);

		// Get the previous month and the next month.
		$dateModel = $this->statsLeadsModel->getDateModel();
		$prevMonth = $dateModel->getPrevMonth();
		$thisMonth = $dateModel->getThisMonth();
		$nextMonth = $dateModel->getNextMonth();
		$prevMonth = $this->monthControlFormatter->format($prevMonth, $formatter);
		$thisMonth = $this->monthControlFormatter->format($thisMonth, $formatter);
		$nextMonth = $this->monthControlFormatter->format($nextMonth, $formatter);

		// Get the Pokémon usage data.
		$pokemonData = $this->statsLeadsModel->getPokemon();
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
			];
		}

		// Navigation breadcrumbs.
		$formatIdentifier = $format->getIdentifier();
		$breadcrumbs = [
			[
				'url' => '/stats',
				'text' => 'Stats',
			],
			[
				'url' => "/stats/$month",
				'text' => $thisMonth['text'],
			],
			[
				'url' => "/stats/$month/$formatIdentifier/$rating",
				'text' => $format->getName(),
			],
			[
				'text' => 'Leads',
			]
		];

		return new JsonResponse([
			'data' => [
				'title' => 'Porydex - Stats - ' . $thisMonth['text'] . ' '
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
				'ratings' => $this->statsLeadsModel->getRatings(),

				// The main data.
				'pokemons' => $pokemons,
			]
		]);
	}
}
