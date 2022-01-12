<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\EggGroups;

use Jp\Dex\Domain\Languages\LanguageId;

interface EggGroupNameRepositoryInterface
{
	/**
	 * Get an egg group name by language and egg group.
	 */
	public function getByLanguageAndEggGroup(
		LanguageId $languageId,
		EggGroupId $eggGroupId
	) : EggGroupName;
}
