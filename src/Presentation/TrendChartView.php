<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\TrendChartModel;
use Psr\Http\Message\ResponseInterface;
use Twig_Environment;
use Zend\Diactoros\Response;
use Zend\Diactoros\Response\JsonResponse;

class TrendChartView
{
	/** @var Twig_Environment $twig */
	private $twig;

	/** @var BaseView $baseView */
	private $baseView;

	/** @var TrendChartModel $trendChartModel */
	private $trendChartModel;

	/**
	 * Constructor.
	 *
	 * @param Twig_Environment $twig
	 * @param BaseView $baseView
	 * @param TrendChartModel $trendChartModel
	 */
	public function __construct(
		Twig_Environment $twig,
		BaseView $baseView,
		TrendChartModel $trendChartModel
	) {
		$this->twig = $twig;
		$this->baseView = $baseView;
		$this->trendChartModel = $trendChartModel;
	}

	/**
	 * Show the /stats/trends/chart page.
	 *
	 * @return ResponseInterface
	 */
	public function index() : ResponseInterface
	{
		// Navigational breadcrumbs.
		$breadcrumbs = [
			[
				'url' => '/stats',
				'text' => 'Stats',
			],
			[
				// TODO: url
				'text' => 'Trends',
			],
			[
				// TODO: text: chart title.
				'text' => 'Chart',
			]
		];

		$content = $this->twig->render(
			'html/trend-chart.twig',
			$this->baseView->getBaseVariables() + [
				'title' => 'Porydex - Stats - Trends - Chart',
				'breadcrumbs' => $breadcrumbs,
			]
		);

		$response = new Response();
		$response->getBody()->write($content);
		return $response;
	}

	/**
	 * Set data for the /stats/trends/chart page.
	 *
	 * @return ResponseInterface
	 */
	public function ajax() : ResponseInterface
	{
		$trendLines = $this->trendChartModel->getTrendLines();

		$datasets = [];
		$index = 0; // Used to determine line color.
		foreach ($trendLines as $trendLine) {
			$data = [];
			foreach ($trendLine->getTrendPoints() as $point) {
				$data[] = [
					'x' => $point->getDate()->format('Y-m'),
					'y' => $point->getValue(),
				];
			}

			$datasets[] = [
				'label' => 'TODO',
				'data' => $data,
				'borderColor' => $this->getLineColor($index),
				'fill' => false,
			];

			$index++;
		}

		return new JsonResponse([
			'type' => 'line',
			'data' => [
				'datasets' => $datasets,
			],
			'options' => [
				'title' => [
					'display' => true,
					'text' => 'Chart Title TODO',
					'fontSize' => 16,
				],
				'scales' => [
					'xAxes' => [
						[
							'type' => 'time',
							'time' => [
								'unit' => 'month',
							],
						],
					],
				],
				'tooltips' => [
					'mode' => 'nearest',
					'intersect' => false,
				],
			],
		]);
	}

	/**
	 * Get a color for the line.
	 *
	 * @param int $index
	 *
	 * @return string
	 */
	private function getLineColor(int $index) : string
	{
		// These default line colors were taken from the Chart.js documentation
		// examples: http://www.chartjs.org/samples/latest/utils.js
		return [
			'#ff6384', // red: 'rgb(255, 99, 132)',
			'#ff9f40', // orange: 'rgb(255, 159, 64)',
			'#ffcd56', // yellow: 'rgb(255, 205, 86)',
			'#4bc0c0', // green: 'rgb(75, 192, 192)',
			'#36a2eb', // blue: 'rgb(54, 162, 235)',
			'#9966ff', // purple: 'rgb(153, 102, 255)',
			'#c9cbcf', // grey: 'rgb(201, 203, 207)'
		][$index % 7];
	}
}
