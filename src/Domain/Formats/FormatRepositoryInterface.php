<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Formats;

use Jp\Dex\Domain\Languages\LanguageId;

interface FormatRepositoryInterface
{
	/**
	 * Get a format by its id.
	 *
	 * @throws FormatNotFoundException if no format exists with this id.
	 */
	public function getById(FormatId $formatId, LanguageId $languageId) : Format;

	/**
	 * Get a format by its identifier.
	 *
	 * @throws FormatNotFoundException if no format exists with this identifier.
	 */
	public function getByIdentifier(
		string $identifier,
		LanguageId $languageId
	) : Format;
}
