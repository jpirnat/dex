<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Trends\Lines;

final class MovesetAbilityTrendLine extends TrendLine
{
	private(set) string $abilityName;

	/**
	 * @param TrendPoint[] $trendPoints
	 */
	public function __construct(
		string $formatName,
		int $rating,
		string $pokemonName,
		string $abilityName,
		string $pokemonTypeColorCode,
		array $trendPoints,
	) {
		$this->formatName = $formatName;
		$this->rating = $rating;
		$this->pokemonName = $pokemonName;
		$this->abilityName = $abilityName;
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
		$abilityName = $this->abilityName;

		return "$formatName [$rating] $pokemonName - $abilityName Usage";
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
