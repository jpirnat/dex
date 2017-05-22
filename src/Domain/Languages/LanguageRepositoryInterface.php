<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Languages;

interface LanguageRepositoryInterface
{
	/**
	 * Get a language by its id.
	 *
	 * @param LanguageId $languageId
	 *
	 * @throws LanguageNotFoundException if no language exists with this id.
	 *
	 * @return Language
	 */
	public function getById(LanguageId $languageId) : Language;
}
