<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Items;

use Exception;

interface ItemRepositoryInterface
{
	/**
	 * Get an item by its identifier.
	 *
	 * @param string $identifier
	 *
	 * @throws Exception if no item exists with this identifier.
	 *
	 * @return Item
	 */
	public function getByIdentifier(string $identifier) : Item;
}
