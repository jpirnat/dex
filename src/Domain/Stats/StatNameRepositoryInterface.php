<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats;

use Jp\Dex\Domain\Languages\LanguageId;

interface StatNameRepositoryInterface
{
	/**
	 * Get a stat name by language and stat.
	 *
	 * @throws StatNameNotFoundException if no stat name exists with this
	 *     language and stat.
	 */
	public function getByLanguageAndStat(
		LanguageId $languageId,
		StatId $statId,
	) : StatName;

	/**
	 * Get stat names by language.
	 *
	 * @return StatName[] Indexed by stat id.
	 */
	public function getByLanguage(LanguageId $languageId) : array;
}
