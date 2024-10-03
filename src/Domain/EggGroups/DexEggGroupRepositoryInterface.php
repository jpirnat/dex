<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\EggGroups;

use Jp\Dex\Domain\Languages\LanguageId;

interface DexEggGroupRepositoryInterface
{
	/**
	 * Get a dex egg group by its id.
	 *
	 * @throws EggGroupNotFoundException if no egg group exists with this id.
	 */
	public function getById(
		EggGroupId $eggGroupId,
		LanguageId $languageId,
	) : DexEggGroup;

	/**
	 * Get all dex egg groups.
	 *
	 * @return DexEggGroup[] Ordered by name.
	 */
	public function getAll(LanguageId $languageId) : array;
}
