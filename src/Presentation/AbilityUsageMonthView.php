<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\AbilityUsageMonth\AbilityUsageData;
use Jp\Dex\Application\Models\AbilityUsageMonth\AbilityUsageMonthModel;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response\HtmlResponse;

class AbilityUsageMonthView
{
	/** @var RendererInterface $renderer */
	private $renderer;

	/** @var BaseView $baseView */
	private $baseView;

	/** @var AbilityUsageMonthModel $abilityUsageMonthModel */
	private $abilityUsageMonthModel;

	/** @var IntlFormatterFactory $formatterFactory */
	private $formatterFactory;

	/**
	 * Constructor.
	 *
	 * @param RendererInterface $renderer
	 * @param BaseView $baseView
	 * @param AbilityUsageMonthModel $abilityUsageMonthModel
	 * @param IntlFormatterFactory $formatterFactory
	 */
	public function __construct(
		RendererInterface $renderer,
		BaseView $baseView,
		IntlFormatterFactory $formatterFactory,
		AbilityUsageMonthModel $abilityUsageMonthModel
	) {
		$this->renderer = $renderer;
		$this->baseView = $baseView;
		$this->abilityUsageMonthModel = $abilityUsageMonthModel;
		$this->formatterFactory = $formatterFactory;
	}

	/**
	 * Get usage data to create a list of Pokémon who use a specific ability.
	 *
	 * @return ResponseInterface
	 */
	public function getData() : ResponseInterface
	{
		$month = $this->abilityUsageMonthModel->getMonth();
		$formatIdentifier = $this->abilityUsageMonthModel->getFormatIdentifier();
		$rating = $this->abilityUsageMonthModel->getRating();

		$formatter = $this->formatterFactory->createFor(
			$this->abilityUsageMonthModel->getLanguageId()
		);

		// Get the previous month and the next month.
		$prevMonth = $this->abilityUsageMonthModel->getDateModel()->getPrevMonth();
		$nextMonth = $this->abilityUsageMonthModel->getDateModel()->getNextMonth();

		// Get ability usage data and sort by usage percent.
		$abilityUsageDatas = $this->abilityUsageMonthModel->getAbilityUsageDatas();
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
				'text' => $this->abilityUsageMonthModel->getAbilityName()->getName(),
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
					'show' => $this->abilityUsageMonthModel->doesPrevMonthDataExist(),
					'month' => $prevMonth->format('Y-m'),
					'text' => $formatter->formatMonth($prevMonth),
				],
				'nextMonth' => [
					'show' => $this->abilityUsageMonthModel->doesNextMonthDataExist(),
					'month' => $nextMonth->format('Y-m'),
					'text' => $formatter->formatMonth($nextMonth),
				],
				'ratings' => $this->abilityUsageMonthModel->getRatings(),

				'ability' => [
					'identifier' => $this->abilityUsageMonthModel->getAbilityIdentifier(),
					'name' => $this->abilityUsageMonthModel->getAbilityName()->getName(),
					'description' => $this->abilityUsageMonthModel->getAbilityDescription()->getDescription(),
				],

				// The main data.
				'data' => $data,
			]
		);

		return new HtmlResponse($content);
	}
}
