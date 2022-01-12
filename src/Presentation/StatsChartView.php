<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\StatsChartModel;
use Jp\Dex\Domain\Stats\Trends\Lines\LeadUsageTrendLine;
use Jp\Dex\Domain\Stats\Trends\Lines\MovesetAbilityTrendLine;
use Jp\Dex\Domain\Stats\Trends\Lines\MovesetItemTrendLine;
use Jp\Dex\Domain\Stats\Trends\Lines\MovesetMoveTrendLine;
use Jp\Dex\Domain\Stats\Trends\Lines\TrendLine;
use Jp\Dex\Domain\Stats\Trends\Lines\UsageAbilityTrendLine;
use Jp\Dex\Domain\Stats\Trends\Lines\UsageItemTrendLine;
use Jp\Dex\Domain\Stats\Trends\Lines\UsageMoveTrendLine;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;

final class StatsChartView
{
	public function __construct(
		private StatsChartModel $statsChartModel,
	) {}

	/**
	 * Set data for the /stats/chart page.
	 */
	public function ajax() : ResponseInterface
	{
		$trendLines = $this->statsChartModel->getTrendLines();

		$lines = [];
		$index = 0;
		foreach ($trendLines as $trendLine) {
			$data = [];
			foreach ($trendLine->getTrendPoints() as $point) {
				$data[] = [
					'x' => $point->getDate()->format('Y-m'),
					'y' => $point->getValue(),
				];
			}

			$lines[] = [
				'label' => $this->getLineLabel($trendLine),
				'data' => $data,
				'color' => $this->getLineColor($trendLine, $index),
			];

			$index++;
		}

		return new JsonResponse([
			'data' => [
				'chartTitle' => $this->getChartTitle(),
				'lines' => $lines,
				'locale' => $this->statsChartModel->getLanguage()->getLocale(),
			]
		]);
	}

	/**
	 * Get a title for the chart.
	 */
	private function getChartTitle() : string
	{
		$trendLines = $this->statsChartModel->getTrendLines();
		if (count($trendLines) === 1) {
			// Use the trend line's own chart title rather than generating one ourselves.
			return $trendLines[0]->getChartTitle();
		}

		$similarities = $this->statsChartModel->getSimilarities();

		$trendLine = $this->statsChartModel->getTrendLines()[0];
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
	 */
	private function getLineLabel(TrendLine $trendLine) : string
	{
		$trendLines = $this->statsChartModel->getTrendLines();
		if (count($trendLines) === 1) {
			// Use the trend line's own label rather than generating one ourselves.
			return $trendLine->getLineLabel();
		}

		$differences = $this->statsChartModel->getDifferences();

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
	 */
	private function getLineColor(TrendLine $trendLine, int $index) : string
	{
		$differences = $this->statsChartModel->getDifferences();
		if ($differences === ['rating']) {
			// Special case: For charts where we're looking at the same thing
			// across different rating levels, each rating has a specific color.
			$rating = $trendLine->getRating();
			if ($rating === 0) {
				return 'rgba(0, 0, 0, 1)'; // black
			}
			if ($rating === 1500) {
				return 'rgba(255, 99, 132, 1)'; // red
			}
			if ($rating === 1630 || $rating === 1695) {
				return 'rgba(54, 162, 235, 1)'; // blue
			}
			if ($rating === 1760 || $rating === 1825) {
				return 'rgba(153, 102, 255, 1)'; // purple
			}
			return 'rgba(201, 203, 207, 1)'; // This shouldn't ever happen.
		}

		if ($trendLine instanceof MovesetMoveTrendLine) {
			return $trendLine->getMoveType()->getColorCode();
		}

		if ($trendLine instanceof MovesetAbilityTrendLine || $trendLine instanceof MovesetItemTrendline) {
			// For moveset ability and moveset item lines, use these colors from
			// the Chart.js documentation.
			return [
				'rgba(255, 99, 132, 1)', // red
				'rgba(255, 159, 64, 1)', // orange
				'rgba(255, 206, 86, 1)', // yellow
				'rgba(75, 192, 192, 1)', // green
				'rgba(54, 162, 235, 1)', // blue
				'rgba(153, 102, 255, 1)', // purple
				'rgba(201, 203, 207, 1)', // gray
			][$index % 7];
		}

		// For all other cases, use the color of the Pokémon's primary type.
		return $trendLine->getPokemonType()->getColorCode();
	}
}
