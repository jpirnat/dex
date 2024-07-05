<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Natures;

use Jp\Dex\Domain\Forms\FormId;
use Jp\Dex\Domain\Languages\LanguageId;

interface DexNatureRepositoryInterface
{
	/**
	 * Get the dex natures by language.
	 */
	public function getByLanguage(LanguageId $languageId) : array;

	/**
	 * Get the names of the natures for which Toxel will evolve into this form.
	 *
	 * @return string[]
	 */
	public function getByToxelEvo(
		LanguageId $languageId,
		FormId $toxelEvoId,
	) : array;
}
