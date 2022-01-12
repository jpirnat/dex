<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\StatsAveragedLeads\LeadsData;
use Jp\Dex\Application\Models\StatsAveragedLeads\StatsAveragedLeadsModel;
use Laminas\Diactoros\Response\HtmlResponse;
use Psr\Http\Message\ResponseInterface;

final class StatsAveragedLeadsView
{
	public function __construct(
		private RendererInterface $renderer,
		private BaseView $baseView,
		private StatsAveragedLeadsModel $statsAveragedLeadsModel,
		private IntlFormatterFactory $formatterFactory,
	) {}

	/**
	 * Get leads data averaged over multiple months.
	 */
	public function getData() : ResponseInterface
	{
		$start = $this->statsAveragedLeadsModel->getStart();
		$end = $this->statsAveragedLeadsModel->getEnd();
		$format = $this->statsAveragedLeadsModel->getFormat();
		$rating = $this->statsAveragedLeadsModel->getRating();

		$formatter = $this->formatterFactory->createFor(
			$this->statsAveragedLeadsModel->getLanguageId()
		);

		// Get usage data and sort by rank.
		$leadsDatas = $this->statsAveragedLeadsModel->getLeadsDatas();
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
				'icon' => $leadsData->getFormIcon(),
				'usagePercent' => $formatter->formatPercent($leadsData->getLeadUsagePercent()),
				'raw' => $formatter->formatNumber($leadsData->getRaw()),
				'rawPercent' => $formatter->formatPercent($leadsData->getRawPercent()),
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
				'text' => 'Formats',
			],
			[
				'url' => "/stats/$start-to-$end/$formatIdentifier/$rating",
				'text' => $format->getName(),
			],
			[
				'text' => 'Leads',
			]
		];

		$content = $this->renderer->render(
			'html/stats/averaged-leads.twig',
			$this->baseView->getBaseVariables() + [
				'start' => $start,
				'end' => $end,
				'format' => [
					'identifier' => $format->getIdentifier(),
					'name' => $format->getName(),
				],
				'rating' => $rating,

				'breadcrumbs' => $breadcrumbs,

				'ratings' => $this->statsAveragedLeadsModel->getRatings(),

				// The main data.
				'data' => $data,
			]
		);

		return new HtmlResponse($content);
	}
}
