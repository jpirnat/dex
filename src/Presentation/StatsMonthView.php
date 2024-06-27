<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\StatsMonthModel;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;

final readonly class StatsMonthView
{
	public function __construct(
		private StatsMonthModel $statsMonthModel,
		private IntlFormatterFactory $formatterFactory,
		private MonthControlFormatter $monthControlFormatter,
	) {}

	/**
	 * Get data for the stats month page.
	 */
	public function getData() : ResponseInterface
	{
		$formatter = $this->formatterFactory->createFor(
			$this->statsMonthModel->getLanguageId()
		);

		// Get the previous month and the next month.
		$dateModel = $this->statsMonthModel->getDateModel();
		$prevMonth = $dateModel->getPrevMonth();
		$thisMonth = $dateModel->getThisMonth();
		$nextMonth = $dateModel->getNextMonth();
		$prevMonth = $this->monthControlFormatter->format($prevMonth, $formatter);
		$thisMonth = $this->monthControlFormatter->format($thisMonth, $formatter);
		$nextMonth = $this->monthControlFormatter->format($nextMonth, $formatter);

		$generations = $this->statsMonthModel->getGenerations();

		// Navigation breadcrumbs.
		$breadcrumbs = [[
			'url' => '/stats',
			'text' => 'Stats',
		],[
			'text' => $thisMonth['name'],
		]];

		return new JsonResponse([
			'data' => [
				'title' => 'Porydex - Stats - ' . $thisMonth['name'],

				'breadcrumbs' => $breadcrumbs,
				'prevMonth' => $prevMonth,
				'thisMonth' => $thisMonth,
				'nextMonth' => $nextMonth,

				// The main data.
				'generations' => $generations,
			]
		]);
	}
}
