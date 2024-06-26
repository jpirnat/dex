<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Items;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Versions\VersionGroupId;

interface ItemDescriptionRepositoryInterface
{
	/**
	 * Get an item description by version group, language, and item.
	 */
	public function getByItem(
		VersionGroupId $versionGroupId,
		LanguageId $languageId,
		ItemId $itemId,
	) : ItemDescription;

	/**
	 * Get item descriptions for TMs/HMs/TRs available for this version group,
	 * based on all the version groups that can transfer movesets into this one.
	 *
	 * @return ItemDescription[][] Indexed by version group id, then item id.
	 */
	public function getTmsByIntoVg(
		VersionGroupId $versionGroupId,
		LanguageId $languageId,
	) : array;

	/**
	 * Get item descriptions for TMs/HMs/TRs for this specific move and
	 * available for this version group, based on all the version groups that
	 * can transfer movesets into this one.
	 *
	 * @return ItemDescription[][] Indexed by version group id, then item id.
	 */
	public function getTmsByIntoVgAndMove(
		VersionGroupId $versionGroupId,
		MoveId $moveId,
		LanguageId $languageId,
	) : array;
}
