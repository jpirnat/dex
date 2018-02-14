<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats;

use Jp\Dex\Domain\Languages\LanguageId;

interface StatNameRepositoryInterface
{
	/**
	 * Get stat names by language. Indexed by stat id.
	 *
	 * @param LanguageId $languageId
	 *
	 * @return StatName[]
	 */
	public function getByLanguage(LanguageId $languageId) : array;
}
