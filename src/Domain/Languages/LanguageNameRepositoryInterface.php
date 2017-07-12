<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Languages;

interface LanguageNameRepositoryInterface
{
	/**
	 * Get language names in their own languages. Indexed by language id value.
	 *
	 * @return LanguageName[]
	 */
	public function getInOwnLanguages() : array;
}
