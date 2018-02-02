<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Formats;

use Jp\Dex\Domain\Languages\LanguageId;

interface FormatNameRepositoryInterface
{
	/**
	 * Get a format name by language and format.
	 *
	 * @param LanguageId $languageId
	 * @param FormatId $formatId
	 *
	 * @throws FormatNameNotFoundException if no format name exists for this
	 *     language and format.
	 *
	 * @return FormatName
	 */
	public function getByLanguageAndFormat(
		LanguageId $languageId,
		FormatId $formatId
	) : FormatName;
}
