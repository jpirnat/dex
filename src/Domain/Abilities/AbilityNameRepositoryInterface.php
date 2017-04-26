<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Abilities;

use Exception;
use Jp\Dex\Domain\Languages\LanguageId;

interface AbilityNameRepositoryInterface
{
	/**
	 * Get an ability name by language and ability.
	 *
	 * @param LanguageId $languageId
	 * @param AbilityId $abilityId
	 *
	 * @throws Exception if no name exists.
	 *
	 * @return AbilityName
	 */
	public function getByLanguageAndAbility(
		LanguageId $languageId,
		AbilityId $abilityId
	) : AbilityName;
}
