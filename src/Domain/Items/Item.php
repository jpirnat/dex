<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Items;

use Jp\Dex\Domain\Versions\VersionGroupId;

final class Item
{
	private ItemId $id;
	private string $identifier;
	private VersionGroupId $introducedInVersionGroupId;
	private ?int $itemFlingPower;
	private ?ItemFlingEffectId $itemFlingEffectId;

	/**
	 * Constructor.
	 *
	 * @param ItemId $itemId
	 * @param string $identifier
	 * @param VersionGroupId $introducedInVersionGroupId
	 * @param int|null $itemFlingPower
	 * @param ItemFlingEffectId|null $itemFlingEffectId
	 */
	public function __construct(
		ItemId $itemId,
		string $identifier,
		VersionGroupId $introducedInVersionGroupId,
		?int $itemFlingPower,
		?ItemFlingEffectId $itemFlingEffectId
	) {
		$this->id = $itemId;
		$this->identifier = $identifier;
		$this->introducedInVersionGroupId = $introducedInVersionGroupId;
		$this->itemFlingPower = $itemFlingPower;
		$this->itemFlingEffectId = $itemFlingEffectId;
	}

	/**
	 * Get the item's id.
	 *
	 * @return ItemId
	 */
	public function getId() : ItemId
	{
		return $this->id;
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
	 * Get the version group id this item was introduced in.
	 *
	 * @return VersionGroupId
	 */
	public function getIntroducedInVersionGroupId() : VersionGroupId
	{
		return $this->introducedInVersionGroupId;
	}

	/**
	 * Get the item's fling power.
	 *
	 * @return int|null
	 */
	public function getItemFlingPower() : ?int
	{
		return $this->itemFlingPower;
	}

	/**
	 * Get the item's fling effect id.
	 *
	 * @return ItemFlingEffectId|null
	 */
	public function getItemFlingEffectId() : ?ItemFlingEffectId
	{
		return $this->itemFlingEffectId;
	}
}
