<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Items;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Versions\VersionGroupId;

interface ItemDescriptionRepositoryInterface
{
	/**
	 * Get an item description by version group, language, and item.
	 */
	public function getByItem(
		VersionGroupId $versionGroupId,
		LanguageId $languageId,
		ItemId $itemId,
	) : ItemDescription;
}
