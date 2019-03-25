<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats;

use Jp\Dex\Domain\Languages\LanguageId;

interface StatNameRepositoryInterface
{
	/**
	 * Get stat names by language.
	 *
	 * @param LanguageId $languageId
	 *
	 * @return StatName[] Indexed by stat id.
	 */
	public function getByLanguage(LanguageId $languageId) : array;
}
