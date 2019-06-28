<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Trends\Lines;

use Jp\Dex\Domain\Pokemon\PokemonName;
use Jp\Dex\Domain\Types\Type;

abstract class TrendLine
{
	/** @var string $formatName */
	protected $formatName;

	/** @var int $rating */
	protected $rating;

	/** @var PokemonName $pokemonName */
	protected $pokemonName;

	/** @var Type $pokemonType */
	protected $pokemonType;

	/** @var TrendPoint[] $trendPoints */
	protected $trendPoints = [];

	/**
	 * Add a trend point to the trend line.
	 *
	 * @param TrendPoint $trendPoint
	 *
	 * @return void
	 */
	protected function addTrendPoint(TrendPoint $trendPoint) : void
	{
		$this->trendPoints[] = $trendPoint;
	}

	/**
	 * Get the title of a chart that consists of only this trend line.
	 *
	 * @return string
	 */
	abstract public function getChartTitle() : string;

	/**
	 * Get the trend line's label, for a chart that consists of only this trend
	 * line.
	 *
	 * @return string
	 */
	abstract public function getLineLabel() : string;

	/**
	 * Get the trend line's format name.
	 *
	 * @return string
	 */
	public function getFormatName() : string
	{
		return $this->formatName;
	}

	/**
	 * Get the trend line's rating.
	 *
	 * @return int
	 */
	public function getRating() : int
	{
		return $this->rating;
	}

	/**
	 * Get the trend line's Pokémon name.
	 *
	 * @return PokemonName
	 */
	public function getPokemonName() : PokemonName
	{
		return $this->pokemonName;
	}

	/**
	 * Get the trend line's Pokémon type.
	 *
	 * @return Type
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
