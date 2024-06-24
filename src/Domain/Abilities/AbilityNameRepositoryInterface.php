<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Abilities;

use Jp\Dex\Domain\Languages\LanguageId;

interface AbilityNameRepositoryInterface
{
	/**
	 * Get an ability name by language and ability.
	 *
	 * @throws AbilityNameNotFoundException if no ability name exists for this
	 *     language and ability.
	 */
	public function getByLanguageAndAbility(
		LanguageId $languageId,
		AbilityId $abilityId,
	) : AbilityName;
}
