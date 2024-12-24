<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Moves;

use Exception;

/**
 * An enum for classifying the various kinds of special moves.
 * Not to be confused with the in-game type of a move. ("Normal", "Fire", etc.)
 */
final readonly class MoveType
{
	public const string REGULAR = '';
	public const string Z_MOVE = 'z';
	public const string MAX_MOVE = 'max';
	public const string G_MAX_MOVE = 'g-max';

	private(set) string $value;

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
}
