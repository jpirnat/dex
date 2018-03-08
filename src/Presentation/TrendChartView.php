<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\TrendChartModel;
use Jp\Dex\Domain\Stats\Trends\Lines\MovesetAbilityTrendLine;
use Jp\Dex\Domain\Stats\Trends\Lines\MovesetItemTrendLine;
use Jp\Dex\Domain\Stats\Trends\Lines\MovesetMoveTrendLine;
use Jp\Dex\Domain\Stats\Trends\Lines\TrendLine;
use Jp\Dex\Domain\Stats\Trends\Lines\UsageAbilityTrendLine;
use Jp\Dex\Domain\Stats\Trends\Lines\UsageItemTrendLine;
use Jp\Dex\Domain\Stats\Trends\Lines\UsageMoveTrendLine;
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
				'label' => $this->getLineLabel($trendLine),
				'data' => $data,
				'borderColor' => $this->getLineColor($index),
				'fill' => false,
			];

			$index++;
		}

		return new JsonResponse([
			'locale' => $this->trendChartModel->getLanguage()->getLocale(),
			'chart' => [
				'type' => 'line',
				'data' => [
					'datasets' => $datasets,
				],
				'options' => [
					'title' => [
						'display' => true,
						'text' => $this->getChartTitle(),
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
			],
		]);
	}

	/**
	 * Get a title for the chart.
	 *
	 * @return string
	 */
	private function getChartTitle() : string
	{
		$similarities = $this->trendChartModel->getSimilarities();

		$trendLine = $this->trendChartModel->getTrendLines()[0];
		$formatName = $trendLine->getFormatName()->getName();
		$rating = (string) $trendLine->getRating();
		$pokemonName = $trendLine->getPokemonName()->getName();
		$movesetName = '';
		if ($trendLine instanceof MovesetAbilityTrendLine || $trendLine instanceof UsageAbilityTrendLine) {
			$movesetName = $trendLine->getAbilityName()->getName();
		}
		if ($trendLine instanceof MovesetItemTrendLine || $trendLine instanceof UsageItemTrendLine) {
			$movesetName = $trendLine->getItemName()->getName();
		}
		if ($trendLine instanceof MovesetMoveTrendLine || $trendLine instanceof UsageMoveTrendLine) {
			$movesetName = $trendLine->getMoveName()->getName();
		}

		$titleParts = [];

		if (in_array('format', $similarities)) {
			$titleParts[] = $formatName;
		}

		if (in_array('rating', $similarities)) {
			$titleParts[] = "Rating $rating";
		}

		if (($trendLine instanceof UsageAbilityTrendLine
			|| $trendLine instanceof UsageItemTrendLine
			|| $trendLine instanceof UsageMoveTrendLine)
			&& (in_array('pokemon', $similarities)
			|| in_array('moveset', $similarities))
		) {
			$titleParts[] = "$pokemonName with $movesetName";
		} elseif (in_array('pokemon', $similarities)) {
			$titleParts[] = $pokemonName;
		} elseif (in_array('moveset', $similarities)) {
			$titleParts[] = $movesetName;
		}

		return implode(' - ', $titleParts);
	}

	/**
	 * Get a label for the line.
	 *
	 * @param TrendLine $trendLine
	 *
	 * @return string
	 */
	private function getLineLabel(TrendLine $trendLine) : string
	{
		$differences = $this->trendChartModel->getDifferences();

		$formatName = $trendLine->getFormatName()->getName();
		$rating = (string) $trendLine->getRating();
		$pokemonName = $trendLine->getPokemonName()->getName();
		$movesetName = '';
		if ($trendLine instanceof MovesetAbilityTrendLine || $trendLine instanceof UsageAbilityTrendLine) {
			$movesetName = $trendLine->getAbilityName()->getName();
		}
		if ($trendLine instanceof MovesetItemTrendLine || $trendLine instanceof UsageItemTrendLine) {
			$movesetName = $trendLine->getItemName()->getName();
		}
		if ($trendLine instanceof MovesetMoveTrendLine || $trendLine instanceof UsageMoveTrendLine) {
			$movesetName = $trendLine->getMoveName()->getName();
		}

		$labelParts = [];

		if (in_array('format', $differences)) {
			$labelParts[] = $formatName;
		}

		if (in_array('rating', $differences)) {
			$labelParts[] = "Rating $rating";
		}

		if (($trendLine instanceof UsageAbilityTrendLine
			|| $trendLine instanceof UsageItemTrendLine
			|| $trendLine instanceof UsageMoveTrendLine)
			&& (in_array('pokemon', $differences)
			|| in_array('moveset', $differences))
		) {
			$labelParts[] = "$pokemonName with $movesetName";
		} elseif (in_array('pokemon', $differences)) {
			$labelParts[] = $pokemonName;
		} elseif (in_array('moveset', $differences)) {
			$labelParts[] = $movesetName;
		}

		return implode(' - ', $labelParts);
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
