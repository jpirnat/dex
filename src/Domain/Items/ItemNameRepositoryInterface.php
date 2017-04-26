<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Items;

use Exception;
use Jp\Dex\Domain\Languages\LanguageId;

interface ItemNameRepositoryInterface
{
	/**
	 * Get an item name by language and item.
	 *
	 * @param LanguageId $languageId
	 * @param ItemId $itemId
	 *
	 * @throws Exception if no name exists.
	 *
	 * @return ItemName
	 */
	public function getByLanguageAndItem(
		LanguageId $languageId,
		ItemId $itemId
	) : ItemName;
}
