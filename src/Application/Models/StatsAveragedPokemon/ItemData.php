<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\StatsAveragedPokemon;

final readonly class ItemData
{
	public function __construct(
		private string $itemName,
		private string $itemIdentifier,
		private float $percent,
	) {}

	/**
	 * Get the item name.
	 */
	public function getItemName() : string
	{
		return $this->itemName;
	}

	/**
	 * Get the item identifier.
	 */
	public function getItemIdentifier() : string
	{
		return $this->itemIdentifier;
	}

	/**
	 * Get the percent.
	 */
	public function getPercent() : float
	{
		return $this->percent;
	}
}
