<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Trends\Lines;

use Jp\Dex\Domain\Formats\FormatName;
use Jp\Dex\Domain\Items\ItemName;
use Jp\Dex\Domain\Pokemon\PokemonName;
use Jp\Dex\Domain\Stats\Trends\TrendPoint;

class UsageItemTrendLine extends TrendLine
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
	 * Get the usage item trend line's item name.
	 *
	 * @return ItemName
	 */
	public function getItemName() : ItemName
	{
		return $this->itemName;
	}
}
