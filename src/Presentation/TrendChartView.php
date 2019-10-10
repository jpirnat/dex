<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\TrendChartModel;
use Jp\Dex\Domain\Stats\Trends\Lines\LeadUsageTrendLine;
use Jp\Dex\Domain\Stats\Trends\Lines\MovesetAbilityTrendLine;
use Jp\Dex\Domain\Stats\Trends\Lines\MovesetItemTrendLine;
use Jp\Dex\Domain\Stats\Trends\Lines\MovesetMoveTrendLine;
use Jp\Dex\Domain\Stats\Trends\Lines\TrendLine;
use Jp\Dex\Domain\Stats\Trends\Lines\UsageAbilityTrendLine;
use Jp\Dex\Domain\Stats\Trends\Lines\UsageItemTrendLine;
use Jp\Dex\Domain\Stats\Trends\Lines\UsageMoveTrendLine;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\JsonResponse;

final class TrendChartView
{
	/** @var RendererInterface $renderer */
	private $renderer;

	/** @var BaseView $baseView */
	private $baseView;

	/** @var TrendChartModel $trendChartModel */
	private $trendChartModel;

	/**
	 * Constructor.
	 *
	 * @param RendererInterface $renderer
	 * @param BaseView $baseView
	 * @param TrendChartModel $trendChartModel
	 */
	public function __construct(
		RendererInterface $renderer,
		BaseView $baseView,
		TrendChartModel $trendChartModel
	) {
		$this->renderer = $renderer;
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

		$content = $this->renderer->render(
			'html/trend-chart.twig',
			$this->baseView->getBaseVariables() + [
				'title' => 'Stats - Trends - Chart',
				'breadcrumbs' => $breadcrumbs,
			]
		);

		return new HtmlResponse($content);
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
				'borderColor' => $this->getLineColor($trendLine),
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
						'yAxes' => [
							[
								'ticks' => [
									'beginAtZero' => true,
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
		$trendLines = $this->trendChartModel->getTrendLines();
		if (count($trendLines) === 1) {
			// Use the trend line's own chart title rather than generating one ourselves.
			return $trendLines[0]->getChartTitle();
		}

		$similarities = $this->trendChartModel->getSimilarities();

		$trendLine = $this->trendChartModel->getTrendLines()[0];
		$formatName = $trendLine->getFormatName();
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
		} elseif (in_array('moveset', $similarities) && in_array('type', $similarities)) {
			$titleParts[] = $movesetName;
		}

		if ($trendLine instanceof LeadUsageTrendLine && in_array('type', $similarities)) {
			$titleParts[] = 'Lead Usage';
		}

		if ($titleParts === []) {
			$titleParts[] = 'Usage';
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
		$trendLines = $this->trendChartModel->getTrendLines();
		if (count($trendLines) === 1) {
			// Use the trend line's own label rather than generating one ourselves.
			return $trendLine->getLineLabel();
		}

		$differences = $this->trendChartModel->getDifferences();

		$formatName = $trendLine->getFormatName();
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

		if ($trendLine instanceof LeadUsageTrendLine) {
			$labelParts[] = 'Lead Usage';
		}

		if ($labelParts === []) {
			$labelParts[] = 'Usage';
		}

		return implode(' - ', $labelParts);
	}

	/**
	 * Get a color for the line.
	 *
	 * @param TrendLine $trendLine
	 *
	 * @return string
	 */
	private function getLineColor(TrendLine $trendLine) : string
	{
		return $trendLine->getPokemonType()->getColorCode();

		// These default line colors were taken from the Chart.js documentation
		// examples: http://www.chartjs.org/samples/latest/utils.js
		/*
		return [
			'#ff6384', // red: 'rgb(255, 99, 132)',
			'#ff9f40', // orange: 'rgb(255, 159, 64)',
			'#ffcd56', // yellow: 'rgb(255, 205, 86)',
			'#4bc0c0', // green: 'rgb(75, 192, 192)',
			'#36a2eb', // blue: 'rgb(54, 162, 235)',
			'#9966ff', // purple: 'rgb(153, 102, 255)',
			'#c9cbcf', // grey: 'rgb(201, 203, 207)'
		][$index % 7];
		*/
	}
}
