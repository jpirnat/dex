<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\TypeIcons;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Types\TypeId;
use Jp\Dex\Domain\Versions\GenerationId;

interface TypeIconRepositoryInterface
{
	/**
	 * Get a type icon by its generation, language, and type.
	 *
	 * @param GenerationId $generationId
	 * @param LanguageId $languageId
	 * @param TypeId $typeId
	 *
	 * @throws TypeIconNotFoundException if no type icon exists with this
	 *     generation, language, and type.
	 *
	 * @return TypeIcon
	 */
	public function getByGenerationAndLanguageAndType(
		GenerationId $generationId,
		LanguageId $languageId,
		TypeId $typeId
	) : TypeIcon;

	/**
	 * Get type icons by their generation and language.
	 *
	 * @param GenerationId $generationId
	 * @param LanguageId $languageId
	 *
	 * @return TypeIcon[] Indexed by type id.
	 */
	public function getByGenerationAndLanguage(
		GenerationId $generationId,
		LanguageId $languageId
	) : array;
}
