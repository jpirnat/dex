<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Natures;

use Jp\Dex\Domain\Languages\LanguageId;

final class NatureName
{
	private LanguageId $languageId;
	private NatureId $natureId;
	private string $name;
	private string $description;

	/**
	 * Constructor.
	 *
	 * @param LanguageId $languageId
	 * @param NatureId $natureId
	 * @param string $name
	 * @param string $description
	 */
	public function __construct(
		LanguageId $languageId,
		NatureId $natureId,
		string $name,
		string $description
	) {
		$this->languageId = $languageId;
		$this->natureId = $natureId;
		$this->name = $name;
		$this->description = $description;
	}

	/**
	 * Get the nature name's language id.
	 *
	 * @return LanguageId
	 */
	public function getLanguageId() : LanguageId
	{
		return $this->languageId;
	}

	/**
	 * Get the nature name's nature id.
	 *
	 * @return NatureId
	 */
	public function getNatureId() : NatureId
	{
		return $this->natureId;
	}

	/**
	 * Get the nature name's name value.
	 *
	 * @return string
	 */
	public function getName() : string
	{
		return $this->name;
	}

	/**
	 * Get the nature name's description.
	 *
	 * @return string
	 */
	public function getDescription() : string
	{
		return $this->description;
	}
}
