<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use DateTime;
use Jp\Dex\Application\Models\AveragedUsageModel;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;

final class AveragedUsageView
{
	public function __construct(
		private RendererInterface $renderer,
		private BaseView $baseView,
		private AveragedUsageModel $averagedUsageModel,
		private IntlFormatterFactory $formatterFactory,
		private MonthControlFormatter $monthControlFormatter,
	) {}

	/**
	 * Get usage data averaged over multiple months.
	 */
	public function getData() : ResponseInterface
	{
		$start = $this->averagedUsageModel->getStart();
		$end = $this->averagedUsageModel->getEnd();
		$format = $this->averagedUsageModel->getFormat();
		$rating = $this->averagedUsageModel->getRating();

		$formatter = $this->formatterFactory->createFor(
			$this->averagedUsageModel->getLanguageId()
		);

		// Get the start and end months.
		$startMonth = new DateTime("$start-01");
		$endMonth   = new DateTime("$end-01");
		$startMonth = $this->monthControlFormatter->format($startMonth, $formatter);
		$endMonth   = $this->monthControlFormatter->format($endMonth,   $formatter);

		// Get the Pokémon usage data.
		$pokemonData = $this->averagedUsageModel->getPokemon();
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
			'text' => $format->getName(),
		]];

		return new JsonResponse([
			'data' => [
				'title' => 'Porydex - Stats - ' . $startMonth['name'] . ' through '
					. $endMonth['name'] . ' ' . $format->getName(),
				'format' => [
					'identifier' => $format->getIdentifier(),
					'name' => $format->getName(),
				],
				'rating' => $rating,
				'breadcrumbs' => $breadcrumbs,
				'startMonth' => $startMonth,
				'endMonth' => $endMonth,
				'ratings' => $this->averagedUsageModel->getRatings(),
				'showLeadsLink' => $this->averagedUsageModel->doesLeadsDataExist(),
				'pokemons' => $pokemons,
			]
		]);
	}
}
