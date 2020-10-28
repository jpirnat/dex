<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Languages;

final class LanguageName
{
	public function __construct(
		private LanguageId $inLanguageId,
		private LanguageId $namedLanguageId,
		private string $name,
	) {}

	/**
	 * Get the id of the language the name is in.
	 *
	 * @return LanguageId
	 */
	public function getInLanguageId() : LanguageId
	{
		return $this->inLanguageId;
	}

	/**
	 * Get the id of the language whose name it is.
	 *
	 * @return LanguageId
	 */
	public function getNamedLanguageId() : LanguageId
	{
		return $this->namedLanguageId;
	}

	/**
	 * Get the language name's name value.
	 *
	 * @return string
	 */
	public function getName() : string
	{
		return $this->name;
	}
}
