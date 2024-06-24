<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use DateTime;
use Jp\Dex\Application\Models\StatsIndexModel;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;

final readonly class StatsIndexView
{
	public function __construct(
		private StatsIndexModel $statsIndexModel,
	) {}

	/**
	 * Show the /stats page.
	 */
	public function index() : ResponseInterface
	{
		// Get months. Sort by year ascending, month ascending.
		$months = $this->statsIndexModel->getMonths();
		uasort($months, function (DateTime $a, DateTime $b) : int {
			$comparison = $b->format('Y') <=> $a->format('Y');
			if ($comparison) {
				return $comparison;
			}

			return $a->format('n') <=> $b->format('n');
		});

		// Restructure the data for the template.
		$years = [];
		foreach ($months as $month) {
			$year = (int) $month->format('Y');

			$years[$year]['year'] = $year;
			$years[$year]['months'][] = [
				'value' => $month->format('Y-m'),
				'name' => $month->format('M'),
			];
		}
		$years = array_values($years);

		// Navigational breadcrumbs.
		$breadcrumbs = [[
			'text' => 'Stats',
		]];

		return new JsonResponse([
			'data' => [
				'title' => 'Porydex - Stats',

				'breadcrumbs' => $breadcrumbs,

				'years' => $years,
			]
		]);
	}
}
