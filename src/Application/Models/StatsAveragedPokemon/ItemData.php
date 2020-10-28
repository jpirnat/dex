<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\StatsAveragedPokemon;

final class ItemData
{
	public function __construct(
		private string $itemName,
		private string $itemIdentifier,
		private float $percent,
	) {}

	/**
	 * Get the item name.
	 *
	 * @return string
	 */
	public function getItemName() : string
	{
		return $this->itemName;
	}

	/**
	 * Get the item identifier.
	 *
	 * @return string
	 */
	public function getItemIdentifier() : string
	{
		return $this->itemIdentifier;
	}

	/**
	 * Get the percent.
	 *
	 * @return float
	 */
	public function getPercent() : float
	{
		return $this->percent;
	}
}
