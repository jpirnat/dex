<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Categories;

use Jp\Dex\Domain\Languages\LanguageId;

interface DexCategoryRepositoryInterface
{
	/**
	 * Get dex categories by their language.
	 *
	 * @param LanguageId $languageId
	 *
	 * @return DexCategory[] Indexed by id.
	 */
	public function getByLanguage(LanguageId $languageId) : array;
}
