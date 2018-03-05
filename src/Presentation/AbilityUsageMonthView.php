<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\AbilityUsageMonth\AbilityUsageData;
use Jp\Dex\Application\Models\AbilityUsageMonth\AbilityUsageMonthModel;
use Psr\Http\Message\ResponseInterface;
use Twig_Environment;
use Zend\Diactoros\Response;

class AbilityUsageMonthView
{
	/** @var Twig_Environment $twig */
	private $twig;

	/** @var BaseView $baseView */
	private $baseView;

	/** @var AbilityUsageMonthModel $abilityUsageMonthModel */
	private $abilityUsageMonthModel;

	/** @var IntlFormatterFactory $formatterFactory */
	private $formatterFactory;

	/**
	 * Constructor.
	 *
	 * @param Twig_Environment $twig
	 * @param BaseView $baseView
	 * @param AbilityUsageMonthModel $abilityUsageMonthModel
	 * @param IntlFormatterFactory $formatterFactory
	 */
	public function __construct(
		Twig_Environment $twig,
		BaseView $baseView,
		IntlFormatterFactory $formatterFactory,
		AbilityUsageMonthModel $abilityUsageMonthModel
	) {
		$this->twig = $twig;
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
		$year = $this->abilityUsageMonthModel->getYear();
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
			];
		}

		// Navigation breadcrumbs.
		$breadcrumbs = [
			[
				'url' => '/stats',
				'text' => 'Stats',
			],
			[
				'url' => "/stats/$year/$month",
				'text' => 'Formats',
			],
			[
				'url' => "/stats/$year/$month/$formatIdentifier/$rating",
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

		$content = $this->twig->render(
			'html/ability-usage-month.twig',
			$this->baseView->getBaseVariables() + [
				// TODO: title - "Month Year Format Ability usage stats"?
				'breadcrumbs' => $breadcrumbs,

				// The month control's data.
				'showPrevMonthLink' => $this->abilityUsageMonthModel->doesPrevMonthDataExist(),
				'prevYear' => $prevMonth->getYear(),
				'prevMonth' => $prevMonth->getMonth(),
				'prevMonthText' => $formatter->formatYearMonth($prevMonth),
				'showNextMonthLink' => $this->abilityUsageMonthModel->doesNextMonthDataExist(),
				'nextYear' => $nextMonth->getYear(),
				'nextMonth' => $nextMonth->getMonth(),
				'nextMonthText' => $formatter->formatYearMonth($nextMonth),
				'formatIdentifier' => $formatIdentifier,
				'rating' => $rating,
				'abilityIdentifier' => $this->abilityUsageMonthModel->getAbilityIdentifier(),

				'year' => $year,
				'month' => $month,

				// The main data.
				'data' => $data,
			]
		);

		$response = new Response();
		$response->getBody()->write($content);
		return $response;
	}
}
