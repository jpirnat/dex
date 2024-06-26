<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Items;

final readonly class Item
{
	public function __construct(
		private ItemId $id,
		private string $identifier,
	) {}

	/**
	 * Get the item's id.
	 */
	public function getId() : ItemId
	{
		return $this->id;
	}

	/**
	 * Get the item's identifier.
	 */
	public function getIdentifier() : string
	{
		return $this->identifier;
	}
}
