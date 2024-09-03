<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Types;

use Jp\Dex\Domain\Languages\LanguageId;

interface TypeNameRepositoryInterface
{
	/**
	 * Get a type name by language and type.
	 *
	 * @throws TypeNameNotFoundException if no type name exists for this
	 *     language and type.
	 */
	public function getByLanguageAndType(
		LanguageId $languageId,
		TypeId $typeId,
	) : TypeName;
}
