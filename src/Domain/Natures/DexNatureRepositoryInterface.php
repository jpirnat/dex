<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Natures;

use Jp\Dex\Domain\Languages\LanguageId;

interface DexNatureRepositoryInterface
{
	/**
	 * Get the dex natures by language.
	 */
	public function getByLanguage(LanguageId $languageId) : array;
}
