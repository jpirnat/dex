<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\TypeIcons;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Types\TypeId;

interface TypeIconRepositoryInterface
{
	/**
	 * Get a type icon by its language and type.
	 *
	 * @param LanguageId $languageId
	 * @param TypeId $typeId
	 *
	 * @throws TypeIconNotFoundException if no type icon exists with this
	 *     language and type.
	 *
	 * @return TypeIcon
	 */
	public function getByLanguageAndType(LanguageId $languageId, TypeId $typeId) : TypeIcon;

	/**
	 * Get type icons by their language.
	 *
	 * @param LanguageId $languageId
	 *
	 * @return TypeIcon[] Indexed by type id.
	 */
	public function getByLanguage(LanguageId $languageId) : array;
}
