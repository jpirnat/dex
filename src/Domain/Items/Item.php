<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Items;

use Jp\Dex\Domain\Versions\Generation;

class Item
{
	/** @var ItemId $id */
	private $id;

	/** @var string $identifier */
	private $identifier;

	/** @var Generation $introducedInGeneration */
	private $introducedInGeneration;

	/** @var int|null $itemFlingPower */
	private $itemFlingPower;

	/** @var ItemFlingEffectId|null $itemFlingEffectId */
	private $itemFlingEffectId;

	/**
	 * Constructor.
	 *
	 * @param ItemId $itemId
	 * @param string $identifier
	 * @param Generation $introducedInGeneration
	 * @param int|null $itemFlingPower
	 * @param ItemFlingEffectId|null $itemFlingEffectId
	 */
	public function __construct(
		ItemId $itemId,
		string $identifier,
		Generation $introducedInGeneration,
		?int $itemFlingPower,
		?ItemFlingEffectId $itemFlingEffectId
	) {
		$this->id = $itemId;
		$this->identifier = $identifier;
		$this->introducedInGeneration = $introducedInGeneration;
		$this->itemFlingPower = $itemFlingPower;
		$this->itemFlingEffectId = $itemFlingEffectId;
	}

	/**
	 * Get the item's id.
	 *
	 * @return ItemId
	 */
	public function id() : ItemId
	{
		return $this->id;
	}

	/**
	 * Get the item's identifier.
	 *
	 * @return string
	 */
	public function identifier() : string
	{
		return $this->identifier;
	}

	/**
	 * Get the generation this item was introduced in.
	 *
	 * @return Generation
	 */
	public function introducedInGeneration() : Generation
	{
		return $this->introducedInGeneration;
	}

	/**
	 * Get the item's fling power.
	 *
	 * @return int|null
	 */
	public function itemFlingPower() : ?int
	{
		return $this->itemFlingPower;
	}

	/**
	 * Get the item's fling effect id.
	 *
	 * @return ItemFlingEffectId|null
	 */
	public function itemFlingEffectId() : ?ItemFlingEffectId
	{
		return $this->itemFlingEffectId;
	}
}
