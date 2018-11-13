<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Types;

use Jp\Dex\Domain\Languages\LanguageId;

interface TypeNameRepositoryInterface
{
	/**
	 * Get a type name by language and type.
	 *
	 * @param LanguageId $languageId
	 * @param TypeId $typeId
	 *
	 * @throws TypeNameNotFoundException if no type name exists for this
	 *     language and type.
	 *
	 * @return TypeName
	 */
	public function getByLanguageAndType(
		LanguageId $languageId,
		TypeId $typeId
	) : TypeName;
}
