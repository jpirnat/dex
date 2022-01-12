<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Items;

final class StatsPokemonItem
{
	public function __construct(
		private string $icon,
		private string $identifier,
		private string $name,
		private float $percent,
		private float $change,
	) {}

	/**
	 * Get the item's icon.
	 */
	public function getIcon() : string
	{
		return $this->icon;
	}

	/**
	 * Get the item's identifier.
	 */
	public function getIdentifier() : string
	{
		return $this->identifier;
	}

	/**
	 * Get the item's name.
	 */
	public function getName() : string
	{
		return $this->name;
	}

	/**
	 * Get the item's percent.
	 */
	public function getPercent() : float
	{
		return $this->percent;
	}

	/**
	 * Get the item's change.
	 */
	public function getChange() : float
	{
		return $this->change;
	}
}
