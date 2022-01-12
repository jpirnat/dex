<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Languages;

use Jp\Dex\Domain\EntityId;

final class LanguageId extends EntityId
{
	private const JAPANESE = 1;
	public const ENGLISH = 2;
	private const JAPANESE_KANJI = 8;

	/**
	 * Is this a Japanese language?
	 */
	public function isJapanese() : bool
	{
		return $this->id === self::JAPANESE
			|| $this->id === self::JAPANESE_KANJI
		;
	}
}
