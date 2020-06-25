<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Natures;

use Jp\Dex\Domain\Languages\LanguageId;

interface DexNatureRepositoryInterface
{
	/**
	 * Get the dex natures by language.
	 *
	 * @param LanguageId $languageId
	 *
	 * @return array
	 */
	public function getByLanguage(LanguageId $languageId) : array;
}
