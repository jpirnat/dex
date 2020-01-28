<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\StatsAbilityModel;
use Laminas\Diactoros\Response\HtmlResponse;
use Psr\Http\Message\ResponseInterface;

final class StatsAbilityView
{
	/** @var RendererInterface $renderer */
	private $renderer;

	/** @var BaseView $baseView */
	private $baseView;

	/** @var StatsAbilityModel $statsAbilityModel */
	private $statsAbilityModel;

	/** @var IntlFormatterFactory $formatterFactory */
	private $formatterFactory;

	/** @var MonthControlFormatter $monthControlFormatter */
	private $monthControlFormatter;

	/**
	 * Constructor.
	 *
	 * @param RendererInterface $renderer
	 * @param BaseView $baseView
	 * @param StatsAbilityModel $statsAbilityModel
	 * @param IntlFormatterFactory $formatterFactory
	 * @param MonthControlFormatter $monthControlFormatter
	 */
	public function __construct(
		RendererInterface $renderer,
		BaseView $baseView,
		StatsAbilityModel $statsAbilityModel,
		IntlFormatterFactory $formatterFactory,
		MonthControlFormatter $monthControlFormatter
	) {
		$this->renderer = $renderer;
		$this->baseView = $baseView;
		$this->statsAbilityModel = $statsAbilityModel;
		$this->formatterFactory = $formatterFactory;
		$this->monthControlFormatter = $monthControlFormatter;
	}

	/**
	 * Get usage data to create a list of Pokémon who use a specific ability.
	 *
	 * @return ResponseInterface
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
		$formatIdentifier = $format->getIdentifier();
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
				'format' => [
					'identifier' => $format->getIdentifier(),
					'name' => $format->getName(),
				],
				'rating' => $rating,

				'breadcrumbs' => $breadcrumbs,

				'prevMonth' => $this->monthControlFormatter->format($prevMonth, $formatter),
				'nextMonth' => $this->monthControlFormatter->format($nextMonth, $formatter),
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
