<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Languages;

interface LanguageNameRepositoryInterface
{
	/**
	 * Get language names in their own languages.
	 *
	 * @return LanguageName[] Indexed by language id.
	 */
	public function getInOwnLanguages() : array;
}
