<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Formats;

use Jp\Dex\Domain\Languages\LanguageId;

final class FormatName
{
	private LanguageId $languageId;
	private FormatId $formatId;
	private string $name;

	/**
	 * Constructor.
	 *
	 * @param LanguageId $languageId
	 * @param FormatId $formatId
	 * @param string $name
	 */
	public function __construct(
		LanguageId $languageId,
		FormatId $formatId,
		string $name
	) {
		$this->languageId = $languageId;
		$this->formatId = $formatId;
		$this->name = $name;
	}

	/**
	 * Get the format name's language id.
	 *
	 * @return LanguageId
	 */
	public function getLanguageId() : LanguageId
	{
		return $this->languageId;
	}

	/**
	 * Get the format name's format id.
	 *
	 * @return FormatId
	 */
	public function getFormatId() : FormatId
	{
		return $this->formatId;
	}

	/**
	 * Get the format name's name value.
	 *
	 * @return string
	 */
	public function getName() : string
	{
		return $this->name;
	}
}
