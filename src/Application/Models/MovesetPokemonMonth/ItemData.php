<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\MovesetPokemonMonth;

class ItemData
{
	/** @var string $itemName */
	private $itemName;

	/** @var float $percent */
	private $percent;

	/**
	 * Constructor.
	 *
	 * @param string $itemName
	 * @param float $percent
	 */
	public function __construct(
		string $itemName,
		float $percent
	) {
		$this->itemName = $itemName;
		$this->percent = $percent;
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
}
