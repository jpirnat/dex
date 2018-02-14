<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\TypeIcons;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Types\TypeId;
use Jp\Dex\Domain\Versions\Generation;

interface TypeIconRepositoryInterface
{
	/**
	 * Get a type icon by its generation, language, and type.
	 *
	 * @param Generation $generation
	 * @param LanguageId $languageId
	 * @param TypeId $typeId
	 *
	 * @throws TypeIconNotFoundException if no type icon exists with this
	 *     generation, language, and type.
	 *
	 * @return TypeIcon
	 */
	public function getByGenerationAndLanguageAndType(
		Generation $generation,
		LanguageId $languageId,
		TypeId $typeId
	) : TypeIcon;
}
