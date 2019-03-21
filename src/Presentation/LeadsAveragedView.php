<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\LeadsAveraged\LeadsAveragedModel;
use Jp\Dex\Application\Models\LeadsAveraged\LeadsData;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response\HtmlResponse;

class LeadsAveragedView
{
	/** @var RendererInterface $renderer */
	private $renderer;

	/** @var BaseView $baseView */
	private $baseView;

	/** @var LeadsAveragedModel $leadsAveragedModel */
	private $leadsAveragedModel;

	/** @var IntlFormatterFactory $formatterFactory */
	private $formatterFactory;

	/**
	 * Constructor.
	 *
	 * @param RendererInterface $renderer
	 * @param BaseView $baseView
	 * @param LeadsAveragedModel $leadsAveragedModel
	 * @param IntlFormatterFactory $formatterFactory
	 */
	public function __construct(
		RendererInterface $renderer,
		BaseView $baseView,
		LeadsAveragedModel $leadsAveragedModel,
		IntlFormatterFactory $formatterFactory
	) {
		$this->renderer = $renderer;
		$this->baseView = $baseView;
		$this->leadsAveragedModel = $leadsAveragedModel;
		$this->formatterFactory = $formatterFactory;
	}

	/**
	 * Get leads data averaged over multiple months.
	 *
	 * @return ResponseInterface
	 */
	public function getData() : ResponseInterface
	{
		$start = $this->leadsAveragedModel->getStart();
		$end = $this->leadsAveragedModel->getEnd();
		$formatIdentifier = $this->leadsAveragedModel->getFormatIdentifier();
		$rating = $this->leadsAveragedModel->getRating();

		$formatter = $this->formatterFactory->createFor(
			$this->leadsAveragedModel->getLanguageId()
		);

		// Get usage data and sort by rank.
		$leadsDatas = $this->leadsAveragedModel->getLeadsDatas();
		uasort(
			$leadsDatas,
			function (LeadsData $a, LeadsData $b) : int {
				return $a->getRank() <=> $b->getRank();
			}
		);

		// Compile all usage data into the right form.
		$data = [];
		foreach ($leadsDatas as $leadsData) {
			$data[] = [
				'rank' => $leadsData->getRank(),
				'name' => $leadsData->getPokemonName(),
				'showMovesetLink' => $leadsData->getMonths() > 0,
				'identifier' => $leadsData->getPokemonIdentifier(),
				'formIcon' => $leadsData->getFormIcon(),
				'usagePercent' => $formatter->formatPercent($leadsData->getLeadUsagePercent()),
				'raw' => $formatter->formatNumber($leadsData->getRaw()),
				'rawPercent' => $formatter->formatPercent($leadsData->getRawPercent()),
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
				'url' => "/stats/$start-to-$end/$formatIdentifier/$rating",
				'text' => 'Usage',
			],
			[
				'text' => 'Leads',
			]
		];

		$content = $this->renderer->render(
			'html/leads-averaged.twig',
			$this->baseView->getBaseVariables() + [
				'start' => $start,
				'end' => $end,
				'formatIdentifier' => $formatIdentifier,
				'rating' => $rating,

				'breadcrumbs' => $breadcrumbs,

				'ratings' => $this->leadsAveragedModel->getRatings(),

				// The main data.
				'data' => $data,
			]
		);

		return new HtmlResponse($content);
	}
}
