<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Trends\Lines;

use Jp\Dex\Domain\Formats\FormatName;
use Jp\Dex\Domain\Moves\MoveName;
use Jp\Dex\Domain\Pokemon\PokemonName;
use Jp\Dex\Domain\Stats\Trends\TrendPoint;

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
	 * Get the usage move trend line's move name.
	 *
	 * @return MoveName
	 */
	public function getMoveName() : MoveName
	{
		return $this->moveName;
	}
}
