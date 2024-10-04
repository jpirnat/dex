<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Items;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Versions\VersionGroupId;

interface DexItemRepositoryInterface
{
	/**
	 * Get a dex item by its id.
	 */
	public function getById(
		VersionGroupId $versionGroupId,
		ItemId $itemId,
		LanguageId $languageId,
	) : DexItem;

	/**
	 * Get all dex items in this version group.
	 *
	 * @return DexItem[] Ordered by name.
	 */
	public function getByVersionGroup(
		VersionGroupId $versionGroupId,
		LanguageId $languageId,
	) : array;

	/**
	 * Get dex items for this version group's TMs.
	 *
	 * @return DexItem[] Indexed by id.
	 */
	public function getTmsByVg(
		VersionGroupId $versionGroupId,
		LanguageId $languageId,
	) : array;
}
