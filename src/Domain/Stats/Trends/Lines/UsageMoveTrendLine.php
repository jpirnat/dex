<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Trends\Lines;

use Jp\Dex\Domain\Formats\FormatName;
use Jp\Dex\Domain\Moves\MoveName;
use Jp\Dex\Domain\Pokemon\PokemonName;

class UsageMoveTrendLine extends TrendLine
{
	/** @var MoveName $moveName */
	private $moveName;

	/**
	 * Constructor.
	 *
	 * @param FormatName $formatName
	 * @param int $rating
	 * @param PokemonName $pokemonName
	 * @param MoveName $moveName
	 * @param TrendPoint[] $trendPoints
	 */
	public function __construct(
		FormatName $formatName,
		int $rating,
		PokemonName $pokemonName,
		MoveName $moveName,
		array $trendPoints
	) {
		$this->formatName = $formatName;
		$this->rating = $rating;
		$this->pokemonName = $pokemonName;
		$this->moveName = $moveName;

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
		$moveName = $this->moveName->getName();

		return "$formatName [$rating] $pokemonName with $moveName Usage";
	}

	/**
	 * Get the trend line's label, for a chart that consists of only this trend
	 * line.
	 *
	 * @return string
	 */
	public function getLineLabel() : string
	{
		return 'Usage';
	}

	/**
	 * Get the usage move trend line's move name.
	 *
	 * @return MoveName
	 */
	public function getMoveName() : MoveName
	{
		return $this->moveName;
	}
}
