<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Items;

interface ItemRepositoryInterface
{
	/**
	 * Get an item by its identifier.
	 *
	 * @param string $identifier
	 *
	 * @throws ItemNotFoundException if no item exists with this identifier.
	 *
	 * @return Item
	 */
	public function getByIdentifier(string $identifier) : Item;
}
