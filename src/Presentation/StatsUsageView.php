<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\StatsUsageModel;
use Laminas\Diactoros\Response\HtmlResponse;
use Psr\Http\Message\ResponseInterface;

final class StatsUsageView
{
	private RendererInterface $renderer;
	private BaseView $baseView;
	private StatsUsageModel $statsUsageModel;
	private IntlFormatterFactory $formatterFactory;
	private MonthControlFormatter $monthControlFormatter;

	/**
	 * Constructor.
	 *
	 * @param RendererInterface $renderer
	 * @param BaseView $baseView
	 * @param StatsUsageModel $statsUsageModel
	 * @param IntlFormatterFactory $formatterFactory
	 * @param MonthControlFormatter $monthControlFormatter
	 */
	public function __construct(
		RendererInterface $renderer,
		BaseView $baseView,
		StatsUsageModel $statsUsageModel,
		IntlFormatterFactory $formatterFactory,
		MonthControlFormatter $monthControlFormatter
	) {
		$this->renderer = $renderer;
		$this->baseView = $baseView;
		$this->statsUsageModel = $statsUsageModel;
		$this->formatterFactory = $formatterFactory;
		$this->monthControlFormatter = $monthControlFormatter;
	}

	/**
	 * Get usage data to recreate a stats usage file, such as
	 * http://www.smogon.com/stats/2014-11/ou-1695.txt.
	 *
	 * @return ResponseInterface
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
				'showMovesetLink' => $pokemon->getUsagePercent() >= .01,
				'identifier' => $pokemon->getIdentifier(),
				'name' => $pokemon->getName(),
				'usagePercent' => $formatter->formatPercent($pokemon->getUsagePercent()),
				'usageChange' => $pokemon->getUsageChange(),
				'usageChangeText' => $formatter->formatChange($pokemon->getUsageChange()),
				'raw' => $formatter->formatNumber($pokemon->getRaw()),
				'rawPercent' => $formatter->formatPercent($pokemon->getRawPercent()),
				'real' => $formatter->formatNumber($pokemon->getReal()),
				'realPercent' => $formatter->formatPercent($pokemon->getRealPercent()),
			];
		}

		// Navigation breadcrumbs.
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
				'text' => 'Usage',
			],
		];

		$content = $this->renderer->render(
			'html/stats/usage.twig',
			$this->baseView->getBaseVariables() + [
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

				'myFormat' => $this->statsUsageModel->getMyFormat(),
				'myRating' => $this->statsUsageModel->getMyRating(),

				'showLeadsLink' => $this->statsUsageModel->doesLeadsDataExist(),

				// The main data.
				'pokemons' => $pokemons,
			]
		);

		return new HtmlResponse($content);
	}
}
