<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Flags;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Versions\VersionGroupId;

interface FlagRepositoryInterface
{
	/**
	 * Get all dex flags in this version group.
	 *
	 * @return DexFlag[] Indexed by flag id.
	 */
	public function getByVersionGroup(
		VersionGroupId $versionGroupId,
		LanguageId $languageId,
	) : array;

	/**
	 * Get this move's flags.
	 *
	 * @return FlagId[] Indexed by flag id.
	 */
	public function getByMove(
		VersionGroupId $versionGroupId,
		MoveId $moveId,
	) : array;
}
