<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Natures;

use Exception;
use Jp\Dex\Domain\Languages\LanguageId;

interface NatureNameRepositoryInterface
{
	/**
	 * Get a nature name by language and nature.
	 *
	 * @param LanguageId $languageId
	 * @param NatureId $natureId
	 *
	 * @throws Exception if no name exists.
	 *
	 * @return NatureName
	 */
	public function getByLanguageAndNature(
		LanguageId $languageId,
		NatureId $natureId
	) : NatureName;
}
