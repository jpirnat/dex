<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Abilities;

use Jp\Dex\Domain\Languages\LanguageId;

interface AbilityNameRepositoryInterface
{
	/**
	 * Get an ability name by language and ability.
	 *
	 * @param LanguageId $languageId
	 * @param AbilityId $abilityId
	 *
	 * @throws AbilityNameNotFoundException if no ability name exists for this
	 *     language and ability.
	 *
	 * @return AbilityName
	 */
	public function getByLanguageAndAbility(
		LanguageId $languageId,
		AbilityId $abilityId
	) : AbilityName;

	/**
	 * Get ability names by language.
	 *
	 * @param LanguageId $languageId
	 *
	 * @return AbilityName[] Indexed by ability id.
	 */
	public function getByLanguage(LanguageId $languageId) : array;
}
