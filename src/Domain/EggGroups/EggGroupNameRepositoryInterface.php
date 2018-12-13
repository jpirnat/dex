<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\EggGroups;

use Jp\Dex\Domain\Languages\LanguageId;

interface EggGroupNameRepositoryInterface
{
	/**
	 * Get an egg group name by language and egg group.
	 *
	 * @param LanguageId $languageId
	 * @param EggGroupId $eggGroupId
	 *
	 * @return EggGroupName
	 */
	public function getByLanguageAndEggGroup(
		LanguageId $languageId,
		EggGroupId $eggGroupId
	) : EggGroupName;
}
