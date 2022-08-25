<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Moves;

use Exception;

/**
 * An enum for classifying the various kinds of special moves.
 * Not to be confused with the in-game type of a move. ("Normal", "Fire", etc.)
 */
class MoveType
{
	public const REGULAR = '';
	public const Z_MOVE = 'z';
	public const MAX_MOVE = 'max';
	public const G_MAX_MOVE = 'g-max';

	private string $value;

	/**
	 * @throws Exception if $value is invalid.
	 */
	public function __construct(string $value)
	{
		if ($value !== self::REGULAR
			&& $value !== self::Z_MOVE
			&& $value !== self::MAX_MOVE
			&& $value !== self::G_MAX_MOVE
		) {
			throw new Exception("Invalid move type given: $value.");
		}

		$this->value = $value;
	}

	/**
	 * Get the move type's value.
	 */
	public function value() : string
	{
		return $this->value;
	}
}
