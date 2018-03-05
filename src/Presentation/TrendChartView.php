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
		$datasets = [
			[
				'label' => 'First Dataset',
				'data' => [
					['x' => 1, 'y' => 1],
					['x' => 2, 'y' => 1],
					['x' => 3, 'y' => 2],
				],
			],
			[
				'label' => 'Second Dataset',
				'data' => [
					['x' => 2, 'y' => 5],
					['x' => 3, 'y' => 4],
					['x' => 4, 'y' => 3],
				],
			],
		];

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
}
