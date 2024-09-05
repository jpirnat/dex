<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Abilities;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Versions\VersionGroupId;

interface AbilityFlagRepositoryInterface
{
	/**
	 * Get an ability flag by its identifier.
	 *
	 * @throws AbilityFlagNotFoundException if no ability flag exists with this
	 *     identifier.
	 */
	public function getByIdentifier(string $identifier) : AbilityFlag;

	/**
	 * Get all dex ability flags in this version group, with descriptions in
	 * plural form. ("These abilities")
	 *
	 * @return DexAbilityFlag[] Indexed by flag id.
	 */
	public function getByVersionGroupPlural(
		VersionGroupId $versionGroupId,
		LanguageId $languageId,
	) : array;

	/**
	 * Get a dex ability flag, with description in plural form.
	 */
	public function getByIdPlural(
		VersionGroupId $versionGroupId,
		AbilityFlagId $flagId,
		LanguageId $languageId,
	) : DexAbilityFlag;

	/**
	 * Get all dex ability flags in this version group, with descriptions in
	 * singular form. ("This ability")
	 *
	 * @return DexAbilityFlag[] Indexed by flag id.
	 */
	public function getByVersionGroupSingular(
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
