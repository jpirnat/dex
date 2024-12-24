<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Trends\Lines;

final class UsageMoveTrendLine extends TrendLine
{
	private(set) string $moveName;

	/**
	 * @param TrendPoint[] $trendPoints
	 */
	public function __construct(
		string $formatName,
		int $rating,
		string $pokemonName,
		string $moveName,
		string $pokemonTypeColorCode,
		array $trendPoints,
	) {
		$this->formatName = $formatName;
		$this->rating = $rating;
		$this->pokemonName = $pokemonName;
		$this->moveName = $moveName;
		$this->pokemonTypeColorCode = $pokemonTypeColorCode;

		foreach ($trendPoints as $trendPoint) {
			$this->addTrendPoint($trendPoint);
		}
	}

	/**
	 * Get the title of a chart that consists of only this trend line.
	 */
	public function getChartTitle() : string
	{
		$formatName = $this->formatName;
		$rating = $this->rating;
		$pokemonName = $this->pokemonName;
		$moveName = $this->moveName;

		return "$formatName [$rating] $pokemonName with $moveName Usage";
	}

	/**
	 * Get the trend line's label, for a chart that consists of only this trend
	 * line.
	 */
	public function getLineLabel() : string
	{
		return 'Usage %';
	}
}
