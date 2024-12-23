<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use DateTime;
use Jp\Dex\Application\Models\AveragedLeadsModel;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;

final readonly class AveragedLeadsView
{
	public function __construct(
		private AveragedLeadsModel $averagedLeadsModel,
		private IntlFormatterFactory $formatterFactory,
		private MonthControlFormatter $monthControlFormatter,
	) {}

	/**
	 * Get data for the stats averaged leads page.
	 */
	public function getData() : ResponseInterface
	{
		$start = $this->averagedLeadsModel->start;
		$end = $this->averagedLeadsModel->end;
		$format = $this->averagedLeadsModel->format;
		$rating = $this->averagedLeadsModel->rating;

		$formatter = $this->formatterFactory->createFor(
			$this->averagedLeadsModel->languageId
		);

		// Get the start and end months.
		$startMonth = new DateTime("$start-01");
		$endMonth   = new DateTime("$end-01");
		$startMonth = $this->monthControlFormatter->format($startMonth, $formatter);
		$endMonth   = $this->monthControlFormatter->format($endMonth,   $formatter);

		// Get the Pokémon usage data.
		$pokemonData = $this->averagedLeadsModel->pokemon;
		$pokemons = [];
		foreach ($pokemonData as $pokemon) {
			$pokemons[] = [
				'rank' => $pokemon->getRank(),
				'icon' => $pokemon->getIcon(),
				'showMovesetLink' => $pokemon->getNumberOfMonths() > 0,
				'identifier' => $pokemon->getIdentifier(),
				'name' => $pokemon->getName(),
				'usagePercent' => $pokemon->getUsagePercent(),
				'usagePercentText' => $formatter->formatPercent($pokemon->getUsagePercent()),
				'raw' => $pokemon->getRaw(),
				'rawText' => $formatter->formatNumber($pokemon->getRaw()),
				'rawPercent' => $pokemon->getRawPercent(),
				'rawPercentText' => $formatter->formatPercent($pokemon->getRawPercent()),
			];
		}

		// Navigation breadcrumbs.
		$formatIdentifier = $format->identifier;
		$breadcrumbs = [[
			'url' => '/stats',
			'text' => 'Stats',
		], [
			'text' => 'Formats',
		], [
			'url' => "/stats/$start-to-$end/$formatIdentifier/$rating",
			'text' => $format->name,
		], [
			'text' => 'Leads',
		]];

		return new JsonResponse([
			'data' => [
				'title' => 'Porydex - Stats - ' . $startMonth['name'] . ' through '
					. $endMonth['name'] . ' ' . $format->name . ' - Leads',
				'format' => [
					'identifier' => $format->identifier,
					'name' => $format->name,
				],
				'rating' => $rating,
				'breadcrumbs' => $breadcrumbs,
				'startMonth' => $startMonth,
				'endMonth' => $endMonth,
				'ratings' => $this->averagedLeadsModel->ratings,
				'pokemons' => $pokemons,
			]
		]);
	}
}
