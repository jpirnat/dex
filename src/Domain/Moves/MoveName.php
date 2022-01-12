<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Moves;

use Jp\Dex\Domain\Languages\LanguageId;

final class MoveName
{
	public function __construct(
		private LanguageId $languageId,
		private MoveId $moveId,
		private string $name,
	) {}

	/**
	 * Get the move name's language id.
	 */
	public function getLanguageId() : LanguageId
	{
		return $this->languageId;
	}

	/**
	 * Get the move name's move id.
	 */
	public function getMoveId() : MoveId
	{
		return $this->moveId;
	}

	/**
	 * Get the move name's name value.
	 */
	public function getName() : string
	{
		return $this->name;
	}
}
