<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Items;

use Jp\Dex\Domain\Languages\LanguageId;

interface ItemNameRepositoryInterface
{
	/**
	 * Get an item name by language and item.
	 *
	 * @throws ItemNameNotFoundException if no item name exists for this
	 *     language and item.
	 */
	public function getByLanguageAndItem(
		LanguageId $languageId,
		ItemId $itemId,
	) : ItemName;
}
