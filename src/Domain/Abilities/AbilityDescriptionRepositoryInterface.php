<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Abilities;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Versions\VersionGroupId;

interface AbilityDescriptionRepositoryInterface
{
	/**
	 * Get an ability description by version group, language, and ability.
	 */
	public function getByAbility(
		VersionGroupId $versionGroupId,
		LanguageId $languageId,
		AbilityId $abilityId,
	) : AbilityDescription;
}
