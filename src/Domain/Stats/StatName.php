<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats;

use Jp\Dex\Domain\Languages\LanguageId;

class StatName
{
	/** @var LanguageId $languageId */
	private $languageId;

	/** @var StatId $statId */
	private $statId;

	/** @var string $name */
	private $name;

	/** @var string $abbreviation */
	private $abbreviation;

	/**
	 * Constructor.
	 *
	 * @param LanguageId $languageId
	 * @param StatId $statId
	 * @param string $name
	 * @param string $abbreviation
	 */
	public function __construct(
		LanguageId $languageId,
		StatId $statId,
		string $name,
		string $abbreviation
	) {
		$this->languageId = $languageId;
		$this->statId = $statId;
		$this->name = $name;
		$this->abbreviation = $abbreviation;
	}

	/**
	 * Get the stat name's language id.
	 *
	 * @return LanguageId
	 */
	public function getLanguageId() : LanguageId
	{
		return $this->languageId;
	}

	/**
	 * Get the stat name's stat id.
	 *
	 * @return StatId
	 */
	public function getStatId() : StatId
	{
		return $this->statId;
	}

	/**
	 * Get the stat name's name value.
	 *
	 * @return string
	 */
	public function getName() : string
	{
		return $this->name;
	}

	/**
	 * Get the stat name's abbreviation.
	 *
	 * @return string
	 */
	public function getAbbreviation() : string
	{
		return $this->abbreviation;
	}
}
