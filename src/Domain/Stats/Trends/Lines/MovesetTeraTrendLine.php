<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Trends\Lines;

final class MovesetTeraTrendLine extends TrendLine
{
	private(set) string $typeName;
	private(set) string $teraTypeColorCode;

	/**
	 * Constructor.
	 *
	 * @param TrendPoint[] $trendPoints
	 */
	public function __construct(
		string $formatName,
		int $rating,
		string $pokemonName,
		string $typeName,
		string $pokemonTypeColorCode,
		string $teraTypeColorCode,
		array $trendPoints,
	) {
		$this->formatName = $formatName;
		$this->rating = $rating;
		$this->pokemonName = $pokemonName;
		$this->typeName = $typeName;
		$this->pokemonTypeColorCode = $pokemonTypeColorCode;
		$this->teraTypeColorCode = $teraTypeColorCode;

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
		$typeName = $this->typeName;

		return "$formatName [$rating] $pokemonName - Tera $typeName Usage";
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
