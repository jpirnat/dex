<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Natures;

use Jp\Dex\Domain\Languages\LanguageId;

class NatureName
{
	/** @var LanguageId $languageId */
	protected $languageId;

	/** @var NatureId $natureId */
	protected $natureId;

	/** @var string $name */
	protected $name;

	/**
	 * Constructor.
	 *
	 * @param LanguageId $languageId
	 * @param NatureId $natureId
	 * @param string $name
	 */
	public function __construct(
		LanguageId $languageId,
		NatureId $natureId,
		string $name
	) {
		$this->languageId = $languageId;
		$this->natureId = $natureId;
		$this->name = $name;
	}

	/**
	 * Get the nature name's language id.
	 *
	 * @return LanguageId
	 */
	public function languageId() : LanguageId
	{
		return $this->languageId;
	}

	/**
	 * Get the nature name's nature id.
	 *
	 * @return NatureId
	 */
	public function natureId() : NatureId
	{
		return $this->natureId;
	}

	/**
	 * Get the nature name's name value.
	 *
	 * @return string
	 */
	public function name() : string
	{
		return $this->name;
	}
}
