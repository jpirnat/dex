<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Formats;

use Jp\Dex\Domain\Languages\LanguageId;

interface FormatRepositoryInterface
{
	/**
	 * Get a format by its id.
	 *
	 * @param FormatId $formatId
	 * @param LanguageId $languageId
	 *
	 * @throws FormatNotFoundException if no format exists with this id.
	 *
	 * @return Format
	 */
	public function getById(FormatId $formatId, LanguageId $languageId) : Format;

	/**
	 * Get a format by its identifier.
	 *
	 * @param string $identifier
	 * @param LanguageId $languageId
	 *
	 * @throws FormatNotFoundException if no format exists with this identifier.
	 *
	 * @return Format
	 */
	public function getByIdentifier(
		string $identifier,
		LanguageId $languageId
	) : Format;
}
