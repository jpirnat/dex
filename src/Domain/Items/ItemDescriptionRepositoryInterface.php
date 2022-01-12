<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Items;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Versions\GenerationId;

interface ItemDescriptionRepositoryInterface
{
	/**
	 * Get an item description by generation, language, and item.
	 */
	public function getByGenerationAndLanguageAndItem(
		GenerationId $generationId,
		LanguageId $languageId,
		ItemId $itemId
	) : ItemDescription;
}
