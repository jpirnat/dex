<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Trends\Lines;

use Jp\Dex\Domain\Formats\FormatName;
use Jp\Dex\Domain\Items\ItemName;
use Jp\Dex\Domain\Pokemon\PokemonName;

class MovesetItemTrendLine extends TrendLine
{
	/** @var ItemName $itemName */
	private $itemName;

	/**
	 * Constructor.
	 *
	 * @param FormatName $formatName
	 * @param int $rating
	 * @param PokemonName $pokemonName
	 * @param ItemName $itemName
	 * @param TrendPoint[] $trendPoints
	 */
	public function __construct(
		FormatName $formatName,
		int $rating,
		PokemonName $pokemonName,
		ItemName $itemName,
		array $trendPoints
	) {
		$this->formatName = $formatName;
		$this->rating = $rating;
		$this->pokemonName = $pokemonName;
		$this->itemName = $itemName;

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
		$itemName = $this->itemName->getName();

		return "$formatName [$rating] $pokemonName - $itemName Usage";
	}

	/**
	 * Get the moveset item trend line's item name.
	 *
	 * @return ItemName
	 */
	public function getItemName() : ItemName
	{
		return $this->itemName;
	}
}
