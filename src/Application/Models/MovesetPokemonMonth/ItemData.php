<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\MovesetPokemonMonth;

class ItemData
{
	/** @var string $itemName */
	private $itemName;

	/** @var float $percent */
	private $percent;

	/** @var float $change */
	private $change;

	/**
	 * Constructor.
	 *
	 * @param string $itemName
	 * @param float $percent
	 * @param float $change
	 */
	public function __construct(
		string $itemName,
		float $percent,
		float $change
	) {
		$this->itemName = $itemName;
		$this->percent = $percent;
		$this->change = $change;
	}

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
	 * Get the percent.
	 *
	 * @return float
	 */
	public function getPercent() : float
	{
		return $this->percent;
	}

	/**
	 * Get the change.
	 *
	 * @return float
	 */
	public function getChange() : float
	{
		return $this->change;
	}
}
