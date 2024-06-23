<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Items;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Versions\GenerationId;
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
	 * Get item descriptions for TMs/HMs/TRs between these generations.
	 *
	 * @return ItemDescription[][] Indexed by version group id, then item id.
	 */
	public function getTmsBetween(
		GenerationId $begin,
		GenerationId $end,
		LanguageId $languageId,
	) : array;

	/**
	 * Get item descriptions for TMs/HMs/TRs between these generations for this
	 * specific move.
	 *
	 * @return ItemDescription[][] Indexed by version group id, then item id.
	 */
	public function getByTmMoveBetween(
		GenerationId $begin,
		GenerationId $end,
		MoveId $moveId,
		LanguageId $languageId,
	) : array;
}
