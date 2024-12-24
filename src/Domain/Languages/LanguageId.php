<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Languages;

use Jp\Dex\Domain\EntityId;

final class LanguageId extends EntityId
{
	private const int JAPANESE = 1;
	public const int ENGLISH = 2;
	private const int JAPANESE_KANJI = 8;

	/**
	 * Is this a Japanese language?
	 */
	public function isJapanese() : bool
	{
		return $this->value === self::JAPANESE
			|| $this->value === self::JAPANESE_KANJI
		;
	}
}
