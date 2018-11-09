<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Natures;

use Jp\Dex\Domain\Languages\LanguageId;

interface NatureNameRepositoryInterface
{
	/**
	 * Get a nature name by language and nature.
	 *
	 * @param LanguageId $languageId
	 * @param NatureId $natureId
	 *
	 * @throws NatureNameNotFoundException if no nature name exists for this
	 *     language and nature.
	 *
	 * @return NatureName
	 */
	public function getByLanguageAndNature(
		LanguageId $languageId,
		NatureId $natureId
	) : NatureName;

	/**
	 * Get nature names by language.
	 *
	 * @param LanguageId $languageId
	 *
	 * @return NatureName[] Indexed by nature id.
	 */
	public function getByLanguage(LanguageId $languageId) : array;
}
