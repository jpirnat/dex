<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats;

use Jp\Dex\Domain\Languages\LanguageId;

final class StatName
{
	public function __construct(
		private LanguageId $languageId,
		private StatId $statId,
		private string $name,
		private string $abbreviation,
	) {}

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
