<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Types;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Versions\VersionGroupId;

interface DexTypeRepositoryInterface
{
	/**
	 * Get a dex type by its id.
	 *
	 * @throws TypeNotFoundException if no type exists with this id.
	 */
	public function getById(
		TypeId $typeId,
		LanguageId $languageId,
	) : DexType;

	/**
	 * Get the main dex types available in this version group.
	 *
	 * @return DexType[] Indexed by type id.
	 */
	public function getMainByVersionGroup(
		VersionGroupId $versionGroupId,
		LanguageId $languageId,
	) : array;

	/**
	 * Get the dex types available in this version group.
	 *
	 * @return DexType[] Indexed by type id.
	 */
	public function getByVersionGroup(
		VersionGroupId $versionGroupId,
		LanguageId $languageId,
	) : array;
}
