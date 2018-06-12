<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\UsageAveraged\UsageAveragedModel;
use Jp\Dex\Application\Models\UsageAveraged\UsageData;
use Psr\Http\Message\ResponseInterface;
use Twig_Environment;
use Zend\Diactoros\Response;

class UsageAveragedView
{
	/** @var Twig_Environment $twig */
	private $twig;

	/** @var BaseView $baseView */
	private $baseView;

	/** @var UsageAveragedModel $usageAveragedModel */
	private $usageAveragedModel;

	/** @var IntlFormatterFactory $formatterFactory */
	private $formatterFactory;

	/**
	 * Constructor.
	 *
	 * @param Twig_Environment $twig
	 * @param BaseView $baseView
	 * @param UsageAveragedModel $usageAveragedModel
	 * @param IntlFormatterFactory $formatterFactory
	 */
	public function __construct(
		Twig_Environment $twig,
		BaseView $baseView,
		UsageAveragedModel $usageAveragedModel,
		IntlFormatterFactory $formatterFactory
	) {
		$this->twig = $twig;
		$this->baseView = $baseView;
		$this->usageAveragedModel = $usageAveragedModel;
		$this->formatterFactory = $formatterFactory;
	}

	/**
	 * Get usage data averaged over multiple months.
	 *
	 * @return ResponseInterface
	 */
	public function getData() : ResponseInterface
	{
		$start = $this->usageAveragedModel->getStart();
		$end = $this->usageAveragedModel->getEnd();
		$formatIdentifier = $this->usageAveragedModel->getFormatIdentifier();
		$rating = $this->usageAveragedModel->getRating();

		$formatter = $this->formatterFactory->createFor(
			$this->usageAveragedModel->getLanguageId()
		);

		// Get usage data and sort by rank.
		$usageDatas = $this->usageAveragedModel->getUsageDatas();
		uasort(
			$usageDatas,
			function (UsageData $a, UsageData $b) : int {
				return $a->getRank() <=> $b->getRank();
			}
		);

		// Compile all usage data into the right form.
		$data = [];
		foreach ($usageDatas as $usageData) {
			$data[] = [
				'rank' => $usageData->getRank(),
				'name' => $usageData->getPokemonName(),
				'showMovesetLink' => $usageData->getMonths() > 0,
				'identifier' => $usageData->getPokemonIdentifier(),
				'formIcon' => $usageData->getFormIcon(),
				'usagePercent' => $formatter->formatPercent($usageData->getUsagePercent()),
				'raw' => $formatter->formatNumber($usageData->getRaw()),
				'rawPercent' => $formatter->formatPercent($usageData->getRawPercent()),
				'real' => $formatter->formatNumber($usageData->getReal()),
				'realPercent' => $formatter->formatPercent($usageData->getRealPercent()),
			];
		}

		// Navigation breadcrumbs.
		$breadcrumbs = [
			[
				'url' => '/stats',
				'text' => 'Stats',
			],
			[
				'text' => 'Formats',
			],
			[
				'text' => 'Usage',
			],
		];

		$content = $this->twig->render(
			'html/usage-averaged.twig',
			$this->baseView->getBaseVariables() + [
				'breadcrumbs' => $breadcrumbs,

				'showLeadsLink' => $this->usageAveragedModel->doesLeadsDataExist(),
				'formatIdentifier' => $formatIdentifier,
				'rating' => $rating,

				'start' => $start,
				'end' => $end,

				// The main data.
				'data' => $data,
			]
		);

		$response = new Response();
		$response->getBody()->write($content);
		return $response;
	}
}
