<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Trends\Lines;

abstract class TrendLine
{
	protected(set) string $formatName;
	protected(set) int $rating;
	protected(set) string $pokemonName;
	protected(set) string $pokemonTypeColorCode;

	/** @var TrendPoint[] $trendPoints */
	protected(set) array $trendPoints = [];

	/**
	 * Add a trend point to the trend line.
	 */
	protected function addTrendPoint(TrendPoint $trendPoint) : void
	{
		$this->trendPoints[] = $trendPoint;
	}

	/**
	 * Get the title of a chart that consists of only this trend line.
	 */
	abstract public function getChartTitle() : string;

	/**
	 * Get the trend line's label, for a chart that consists of only this trend
	 * line.
	 */
	abstract public function getLineLabel() : string;
}
