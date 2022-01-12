<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Trends\Lines;

use Jp\Dex\Domain\Pokemon\PokemonName;
use Jp\Dex\Domain\Types\Type;

abstract class TrendLine
{
	protected string $formatName;
	protected int $rating;
	protected PokemonName $pokemonName;
	protected Type $pokemonType;

	/** @var TrendPoint[] $trendPoints */
	protected array $trendPoints = [];

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

	/**
	 * Get the trend line's format name.
	 */
	public function getFormatName() : string
	{
		return $this->formatName;
	}

	/**
	 * Get the trend line's rating.
	 */
	public function getRating() : int
	{
		return $this->rating;
	}

	/**
	 * Get the trend line's Pokémon name.
	 */
	public function getPokemonName() : PokemonName
	{
		return $this->pokemonName;
	}

	/**
	 * Get the trend line's Pokémon type.
	 */
	public function getPokemonType() : Type
	{
		return $this->pokemonType;
	}

	/**
	 * Get the trend line's trend points.
	 *
	 * @return TrendPoint[]
	 */
	public function getTrendPoints() : array
	{
		return $this->trendPoints;
	}
}
