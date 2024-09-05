<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Moves;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Versions\VersionGroupId;

interface MoveFlagRepositoryInterface
{
	/**
	 * Get a move flag by its identifier.
	 *
	 * @throws MoveFlagNotFoundException if no move flag exists with this
	 *     identifier.
	 */
	public function getByIdentifier(string $identifier) : MoveFlag;

	/**
	 * Get all dex move flags in this version group, with descriptions in
	 * plural form. ("These moves")
	 *
	 * @return DexMoveFlag[] Indexed by flag id.
	 */
	public function getByVersionGroupPlural(
		VersionGroupId $versionGroupId,
		LanguageId $languageId,
	) : array;

	/**
	 * Get all dex move flags in this version group, with descriptions in
	 * singular form. ("This move")
	 *
	 * @return DexMoveFlag[] Indexed by flag id.
	 */
	public function getByVersionGroupSingular(
		VersionGroupId $versionGroupId,
		LanguageId $languageId,
	) : array;

	/**
	 * Get this move's flags.
	 *
	 * @return MoveFlagId[] Indexed by flag id.
	 */
	public function getByMove(
		VersionGroupId $versionGroupId,
		MoveId $moveId,
	) : array;
}
