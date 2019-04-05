<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\StatsAveragedUsage\StatsAveragedUsageModel;
use Jp\Dex\Application\Models\StatsAveragedUsage\UsageData;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response\HtmlResponse;

class StatsAveragedUsageView
{
	/** @var RendererInterface $renderer */
	private $renderer;

	/** @var BaseView $baseView */
	private $baseView;

	/** @var StatsAveragedUsageModel $statsAveragedUsageModel */
	private $statsAveragedUsageModel;

	/** @var IntlFormatterFactory $formatterFactory */
	private $formatterFactory;

	/**
	 * Constructor.
	 *
	 * @param RendererInterface $renderer
	 * @param BaseView $baseView
	 * @param StatsAveragedUsageModel $statsAveragedUsageModel
	 * @param IntlFormatterFactory $formatterFactory
	 */
	public function __construct(
		RendererInterface $renderer,
		BaseView $baseView,
		StatsAveragedUsageModel $statsAveragedUsageModel,
		IntlFormatterFactory $formatterFactory
	) {
		$this->renderer = $renderer;
		$this->baseView = $baseView;
		$this->statsAveragedUsageModel = $statsAveragedUsageModel;
		$this->formatterFactory = $formatterFactory;
	}

	/**
	 * Get usage data averaged over multiple months.
	 *
	 * @return ResponseInterface
	 */
	public function getData() : ResponseInterface
	{
		$start = $this->statsAveragedUsageModel->getStart();
		$end = $this->statsAveragedUsageModel->getEnd();
		$formatIdentifier = $this->statsAveragedUsageModel->getFormatIdentifier();
		$rating = $this->statsAveragedUsageModel->getRating();

		$formatter = $this->formatterFactory->createFor(
			$this->statsAveragedUsageModel->getLanguageId()
		);

		// Get usage data and sort by rank.
		$usageDatas = $this->statsAveragedUsageModel->getUsageDatas();
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

		$content = $this->renderer->render(
			'html/usage-averaged.twig',
			$this->baseView->getBaseVariables() + [
				'start' => $start,
				'end' => $end,
				'formatIdentifier' => $formatIdentifier,
				'rating' => $rating,

				'breadcrumbs' => $breadcrumbs,

				'ratings' => $this->statsAveragedUsageModel->getRatings(),

				'showLeadsLink' => $this->statsAveragedUsageModel->doesLeadsDataExist(),

				// The main data.
				'data' => $data,
			]
		);

		return new HtmlResponse($content);
	}
}
