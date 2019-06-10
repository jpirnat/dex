<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\StatsUsageModel;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response\HtmlResponse;

class StatsUsageView
{
	/** @var RendererInterface $renderer */
	private $renderer;

	/** @var BaseView $baseView */
	private $baseView;

	/** @var StatsUsageModel $statsUsageModel */
	private $statsUsageModel;

	/** @var IntlFormatterFactory $formatterFactory */
	private $formatterFactory;

	/**
	 * Constructor.
	 *
	 * @param RendererInterface $renderer
	 * @param BaseView $baseView
	 * @param StatsUsageModel $statsUsageModel
	 * @param IntlFormatterFactory $formatterFactory
	 */
	public function __construct(
		RendererInterface $renderer,
		BaseView $baseView,
		StatsUsageModel $statsUsageModel,
		IntlFormatterFactory $formatterFactory
	) {
		$this->renderer = $renderer;
		$this->baseView = $baseView;
		$this->statsUsageModel = $statsUsageModel;
		$this->formatterFactory = $formatterFactory;
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
		$formatIdentifier = $this->statsUsageModel->getFormatIdentifier();
		$rating = $this->statsUsageModel->getRating();

		$formatter = $this->formatterFactory->createFor(
			$this->statsUsageModel->getLanguageId()
		);

		// Get the previous month and the next month.
		$prevMonth = $this->statsUsageModel->getDateModel()->getPrevMonth();
		$nextMonth = $this->statsUsageModel->getDateModel()->getNextMonth();

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
				'usageChangeText' => $formatter->formatPercent($pokemon->getUsageChange()),
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
				'text' => 'Formats',
			],
			[
				'text' => 'Usage',
			],
		];

		$content = $this->renderer->render(
			'html/stats/usage.twig',
			$this->baseView->getBaseVariables() + [
				'month' => $month,
				'formatIdentifier' => $formatIdentifier,
				'rating' => $rating,

				'breadcrumbs' => $breadcrumbs,

				'prevMonth' => [
					'show' => $this->statsUsageModel->doesPrevMonthDataExist(),
					'month' => $prevMonth->format('Y-m'),
					'text' => $formatter->formatMonth($prevMonth),
				],
				'nextMonth' => [
					'show' => $this->statsUsageModel->doesNextMonthDataExist(),
					'month' => $nextMonth->format('Y-m'),
					'text' => $formatter->formatMonth($nextMonth),
				],
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
