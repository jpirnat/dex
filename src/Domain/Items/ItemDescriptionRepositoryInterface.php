<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Items;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Versions\Generation;

interface ItemDescriptionRepositoryInterface
{
	/**
	 * Get an item description by generation, language, and item.
	 *
	 * @param Generation $generation
	 * @param LanguageId $languageId
	 * @param ItemId $itemId
	 *
	 * @throws ItemDescriptionNotFoundException if no item description exists
	 *     for this generation, language, and item.
	 *
	 * @return ItemDescription
	 */
	public function getByGenerationAndLanguageAndItem(
		Generation $generation,
		LanguageId $languageId,
		ItemId $itemId
	) : ItemDescription;
}
