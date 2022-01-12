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
	 */
	public function getInLanguageId() : LanguageId
	{
		return $this->inLanguageId;
	}

	/**
	 * Get the id of the language whose name it is.
	 */
	public function getNamedLanguageId() : LanguageId
	{
		return $this->namedLanguageId;
	}

	/**
	 * Get the language name's name value.
	 */
	public function getName() : string
	{
		return $this->name;
	}
}
