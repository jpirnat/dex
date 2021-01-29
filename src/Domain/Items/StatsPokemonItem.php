<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Items;

final class StatsPokemonItem
{
	private string $icon;
	private string $identifier;
	private string $name;
	private float $percent;
	private float $change;

	/**
	 * Constructor.
	 *
	 * @param string $icon
	 * @param string $identifier
	 * @param string $name
	 * @param float $percent
	 * @param float $change
	 */
	public function __construct(
		string $icon,
		string $identifier,
		string $name,
		float $percent,
		float $change
	) {
		$this->icon = $icon;
		$this->identifier = $identifier;
		$this->name = $name;
		$this->percent = $percent;
		$this->change = $change;
	}

	/**
	 * Get the item's icon.
	 *
	 * @return string
	 */
	public function getIcon() : string
	{
		return $this->icon;
	}

	/**
	 * Get the item's identifier.
	 *
	 * @return string
	 */
	public function getIdentifier() : string
	{
		return $this->identifier;
	}

	/**
	 * Get the item's name.
	 *
	 * @return string
	 */
	public function getName() : string
	{
		return $this->name;
	}

	/**
	 * Get the item's percent.
	 *
	 * @return float
	 */
	public function getPercent() : float
	{
		return $this->percent;
	}

	/**
	 * Get the item's change.
	 *
	 * @return float
	 */
	public function getChange() : float
	{
		return $this->change;
	}
}
