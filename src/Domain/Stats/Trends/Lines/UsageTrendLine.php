<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Trends\Lines;

use Jp\Dex\Domain\Formats\FormatName;
use Jp\Dex\Domain\Pokemon\PokemonName;
use Jp\Dex\Domain\Stats\Trends\TrendPoint;

class UsageTrendLine extends TrendLine
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
}
