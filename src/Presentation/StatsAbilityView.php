<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\StatsAbility\AbilityUsageData;
use Jp\Dex\Application\Models\StatsAbility\StatsAbilityModel;
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

		// Get ability usage data and sort by usage percent.
		$abilityUsageDatas = $this->statsAbilityModel->getAbilityUsageDatas();
		uasort(
			$abilityUsageDatas,
			function (AbilityUsageData $a, AbilityUsageData $b) : int {
				return $b->getUsagePercent() <=> $a->getUsagePercent();
			}
		);

		// Compile all ability usage data into the right form.
		$data = [];
		foreach ($abilityUsageDatas as $abilityUsageData) {
			$data[] = [
				'name' => $abilityUsageData->getPokemonName(),
				'identifier' => $abilityUsageData->getPokemonIdentifier(),
				'formIcon' => $abilityUsageData->getFormIcon(),
				'pokemonPercent' => $formatter->formatPercent($abilityUsageData->getPokemonPercent()),
				'abilityPercent' => $formatter->formatPercent($abilityUsageData->getAbilityPercent()),
				'usagePercent' => $formatter->formatPercent($abilityUsageData->getUsagePercent()),
				'change' => $abilityUsageData->getChange(),
				'changeText' => $formatter->formatPercent($abilityUsageData->getChange()),
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
			'html/ability-usage-month.twig',
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
				'data' => $data,
			]
		);

		return new HtmlResponse($content);
	}
}
