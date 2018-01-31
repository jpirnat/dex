<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Languages;

class Language
{
	/** @var LanguageId $id */
	private $id;

	/** @var string $identifier */
	private $identifier;

	/** @var string $locale */
	private $locale;

	/** @var string $dateFormat */
	private $dateFormat;

	/**
	 * Constructor.
	 *
	 * @param LanguageId $languageId
	 * @param string $identifier
	 * @param string $locale
	 * @param string $dateFormat
	 */
	public function __construct(
		LanguageId $languageId,
		string $identifier,
		string $locale,
		string $dateFormat
	) {
		$this->id = $languageId;
		$this->identifier = $identifier;
		$this->locale = $locale;
		$this->dateFormat = $dateFormat;
	}

	/**
	 * Get the language's id.
	 *
	 * @return LanguageId
	 */
	public function getId() : LanguageId
	{
		return $this->id;
	}

	/**
	 * Get the language's identifier.
	 *
	 * @return string
	 */
	public function getIdentifier() : string
	{
		return $this->identifier;
	}

	/**
	 * Get the language's locale.
	 *
	 * @return string
	 */
	public function getLocale() : string
	{
		return $this->locale;
	}

	/**
	 * Get the language's date format.
	 *
	 * @return string
	 */
	public function getDateFormat() : string
	{
		return $this->dateFormat;
	}
}
