<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Trends\Lines;

use Jp\Dex\Domain\Items\ItemName;
use Jp\Dex\Domain\Pokemon\PokemonName;
use Jp\Dex\Domain\Types\Type;

final class MovesetItemTrendLine extends TrendLine
{
	private(set) ItemName $itemName;

	/**
	 * Constructor.
	 *
	 * @param TrendPoint[] $trendPoints
	 */
	public function __construct(
		string $formatName,
		int $rating,
		PokemonName $pokemonName,
		ItemName $itemName,
		Type $pokemonType,
		array $trendPoints,
	) {
		$this->formatName = $formatName;
		$this->rating = $rating;
		$this->pokemonName = $pokemonName;
		$this->itemName = $itemName;
		$this->pokemonType = $pokemonType;

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
		$itemName = $this->itemName->getName();

		return "$formatName [$rating] $pokemonName - $itemName Usage";
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
