<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Abilities;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Versions\VersionGroupId;

interface AbilityFlagRepositoryInterface
{
	/**
	 * Get all dex ability flags in this version group.
	 *
	 * @return DexAbilityFlag[] Indexed by flag id.
	 */
	public function getByVersionGroup(
		VersionGroupId $versionGroupId,
		LanguageId $languageId,
	) : array;

	/**
	 * Get this ability's flags.
	 *
	 * @return AbilityFlagId[] Indexed by flag id.
	 */
	public function getByAbility(
		VersionGroupId $versionGroupId,
		AbilityId $abilityId,
	) : array;
}
