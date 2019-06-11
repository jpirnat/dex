<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\StatsAbilityModel;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response\HtmlResponse;

class StatsAbilityView
{
	/** @var RendererInterface $renderer */
	private $renderer;

	/** @var BaseView $baseView */
	private $baseView;

	/** @var StatsAbilityModel $statsAbilityModel */
	private $statsAbilityModel;

	/** @var IntlFormatterFactory $formatterFactory */
	private $formatterFactory;

	/**
	 * Constructor.
	 *
	 * @param RendererInterface $renderer
	 * @param BaseView $baseView
	 * @param StatsAbilityModel $statsAbilityModel
	 * @param IntlFormatterFactory $formatterFactory
	 */
	public function __construct(
		RendererInterface $renderer,
		BaseView $baseView,
		IntlFormatterFactory $formatterFactory,
		StatsAbilityModel $statsAbilityModel
	) {
		$this->renderer = $renderer;
		$this->baseView = $baseView;
		$this->statsAbilityModel = $statsAbilityModel;
		$this->formatterFactory = $formatterFactory;
	}

	/**
	 * Get usage data to create a list of Pokémon who use a specific ability.
	 *
	 * @return ResponseInterface
	 */
	public function getData() : ResponseInterface
	{
		$month = $this->statsAbilityModel->getMonth();
		$formatIdentifier = $this->statsAbilityModel->getFormatIdentifier();
		$rating = $this->statsAbilityModel->getRating();

		$formatter = $this->formatterFactory->createFor(
			$this->statsAbilityModel->getLanguageId()
		);

		// Get the previous month and the next month.
		$prevMonth = $this->statsAbilityModel->getDateModel()->getPrevMonth();
		$nextMonth = $this->statsAbilityModel->getDateModel()->getNextMonth();

		// Get the Pokémon usage data.
		$pokemonData = $this->statsAbilityModel->getPokemon();
		$pokemons = [];
		foreach ($pokemonData as $pokemon) {
			$pokemons[] = [
				'icon' => $pokemon->getIcon(),
				'identifier' => $pokemon->getIdentifier(),
				'name' => $pokemon->getName(),
				'pokemonPercent' => $formatter->formatPercent($pokemon->getPokemonPercent()),
				'abilityPercent' => $formatter->formatPercent($pokemon->getAbilityPercent()),
				'usagePercent' => $formatter->formatPercent($pokemon->getUsagePercent()),
				'usageChange' => $pokemon->getUsageChange(),
				'usageChangeText' => $formatter->formatPercent($pokemon->getUsageChange()),
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
				'url' => "/stats/$month/$formatIdentifier/$rating",
				'text' => 'Usage',
			],
			[
				// TODO: url
				'text' => 'Abilities',
			],
			[
				'text' => $this->statsAbilityModel->getAbilityName()->getName(),
			],
		];

		$content = $this->renderer->render(
			'html/stats/ability.twig',
			$this->baseView->getBaseVariables() + [
				'month' => $month,
				'formatIdentifier' => $formatIdentifier,
				'rating' => $rating,

				'breadcrumbs' => $breadcrumbs,

				'prevMonth' => [
					'show' => $this->statsAbilityModel->doesPrevMonthDataExist(),
					'month' => $prevMonth->format('Y-m'),
					'text' => $formatter->formatMonth($prevMonth),
				],
				'nextMonth' => [
					'show' => $this->statsAbilityModel->doesNextMonthDataExist(),
					'month' => $nextMonth->format('Y-m'),
					'text' => $formatter->formatMonth($nextMonth),
				],
				'ratings' => $this->statsAbilityModel->getRatings(),

				'ability' => [
					'identifier' => $this->statsAbilityModel->getAbilityIdentifier(),
					'name' => $this->statsAbilityModel->getAbilityName()->getName(),
					'description' => $this->statsAbilityModel->getAbilityDescription()->getDescription(),
				],

				// The main data.
				'pokemons' => $pokemons,
			]
		);

		return new HtmlResponse($content);
	}
}
