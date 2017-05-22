<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Languages;

class Language
{
	/** @var LanguageId $id */
	private $id;

	/** @var string $identifier */
	private $identifier;

	/**
	 * Constructor.
	 *
	 * @param LanguageId $languageId
	 * @param string $identifier
	 */
	public function __construct(
		LanguageId $languageId,
		string $identifier
	) {
		$this->id = $languageId;
		$this->identifier = $identifier;
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
}
