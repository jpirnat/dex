<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Items;

interface ItemRepositoryInterface
{
	/**
	 * Get an item by its id.
	 *
	 * @throws ItemNotFoundException if no item exists with this id.
	 */
	public function getById(ItemId $itemId) : Item;

	/**
	 * Get an item by its identifier.
	 *
	 * @throws ItemNotFoundException if no item exists with this identifier.
	 */
	public function getByIdentifier(string $identifier) : Item;
}
