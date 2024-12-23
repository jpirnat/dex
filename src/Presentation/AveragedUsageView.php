<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use DateTime;
use Jp\Dex\Application\Models\AveragedUsageModel;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;

final readonly class AveragedUsageView
{
	public function __construct(
		private AveragedUsageModel $averagedUsageModel,
		private IntlFormatterFactory $formatterFactory,
		private MonthControlFormatter $monthControlFormatter,
	) {}

	/**
	 * Get data for the stats averaged usage page.
	 */
	public function getData() : ResponseInterface
	{
		$start = $this->averagedUsageModel->start;
		$end = $this->averagedUsageModel->end;
		$format = $this->averagedUsageModel->format;
		$rating = $this->averagedUsageModel->rating;

		$formatter = $this->formatterFactory->createFor(
			$this->averagedUsageModel->languageId
		);

		// Get the start and end months.
		$startMonth = new DateTime("$start-01");
		$endMonth   = new DateTime("$end-01");
		$startMonth = $this->monthControlFormatter->format($startMonth, $formatter);
		$endMonth   = $this->monthControlFormatter->format($endMonth,   $formatter);

		// Get the Pokémon usage data.
		$pokemonData = $this->averagedUsageModel->pokemon;
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
			'text' => 'Formats',
		], [
			'text' => $format->name,
		]];

		return new JsonResponse([
			'data' => [
				'title' => 'Porydex - Stats - ' . $startMonth['name'] . ' through '
					. $endMonth['name'] . ' ' . $format->name,
				'format' => [
					'identifier' => $format->identifier,
					'name' => $format->name,
				],
				'rating' => $rating,
				'breadcrumbs' => $breadcrumbs,
				'startMonth' => $startMonth,
				'endMonth' => $endMonth,
				'ratings' => $this->averagedUsageModel->ratings,
				'showLeadsLink' => $this->averagedUsageModel->showLeadsLink,
				'pokemons' => $pokemons,
			]
		]);
	}
}
