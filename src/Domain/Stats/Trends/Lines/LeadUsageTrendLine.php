<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Trends\Lines;

use Jp\Dex\Domain\Formats\FormatName;
use Jp\Dex\Domain\Pokemon\PokemonName;

class LeadUsageTrendLine extends TrendLine
{
	/**
	 * Constructor.
	 *
	 * @param FormatName $formatName
	 * @param int $rating
	 * @param PokemonName $pokemonName
	 * @param TrendPoint[] $trendPoints
	 */
	public function __construct(
		FormatName $formatName,
		int $rating,
		PokemonName $pokemonName,
		array $trendPoints
	) {
		$this->formatName = $formatName;
		$this->rating = $rating;
		$this->pokemonName = $pokemonName;

		foreach ($trendPoints as $trendPoint) {
			$this->addTrendPoint($trendPoint);
		}
	}

	/**
	 * Get the title of a chart that consists of only this trend line.
	 *
	 * @return string
	 */
	public function getChartTitle() : string
	{
		$formatName = $this->formatName->getName();
		$rating = $this->rating;
		$pokemonName = $this->pokemonName->getName();

		return "$formatName [$rating] $pokemonName Lead Usage";
	}
}
