<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Languages;

use Jp\Dex\Domain\EntityId;

final class LanguageId extends EntityId
{
	/** @var int $JAPANESE */
	public const JAPANESE = 1;

	/** @var int $ENGLISH */
	public const ENGLISH = 2;

	/** @var int $JAPANESE_KANJI */
	public const JAPANESE_KANJI = 8;

	/**
	 * Is this a Japanese language?
	 *
	 * @return bool
	 */
	public function isJapanese() : bool
	{
		return $this->id === self::JAPANESE
			|| $this->id === self::JAPANESE_KANJI
		;
	}
}
