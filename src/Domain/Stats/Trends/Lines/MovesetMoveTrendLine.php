<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Trends\Lines;

use Jp\Dex\Domain\Moves\MoveName;
use Jp\Dex\Domain\Pokemon\PokemonName;
use Jp\Dex\Domain\Types\Type;

final class MovesetMoveTrendLine extends TrendLine
{
	private(set) MoveName $moveName;
	private(set) Type $moveType;

	/**
	 * Constructor.
	 *
	 * @param TrendPoint[] $trendPoints
	 */
	public function __construct(
		string $formatName,
		int $rating,
		PokemonName $pokemonName,
		MoveName $moveName,
		Type $pokemonType,
		Type $moveType,
		array $trendPoints,
	) {
		$this->formatName = $formatName;
		$this->rating = $rating;
		$this->pokemonName = $pokemonName;
		$this->moveName = $moveName;
		$this->pokemonType = $pokemonType;
		$this->moveType = $moveType;

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
		$pokemonName = $this->pokemonName->getName();
		$moveName = $this->moveName->name;

		return "$formatName [$rating] $pokemonName - $moveName Usage";
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
