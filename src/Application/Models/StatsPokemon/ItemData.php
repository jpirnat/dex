<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\StatsPokemon;

class ItemData
{
	/** @var string $itemName */
	private $itemName;

	/** @var string $itemIdentifier */
	private $itemIdentifier;

	/** @var float $percent */
	private $percent;

	/** @var float $change */
	private $change;

	/**
	 * Constructor.
	 *
	 * @param string $itemName
	 * @param string $itemIdentifier
	 * @param float $percent
	 * @param float $change
	 */
	public function __construct(
		string $itemName,
		string $itemIdentifier,
		float $percent,
		float $change
	) {
		$this->itemName = $itemName;
		$this->itemIdentifier = $itemIdentifier;
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
