<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats;

use Jp\Dex\Domain\EntityId;
use Jp\Dex\Domain\Versions\GenerationId;

final class StatId extends EntityId
{
	public const int HP = 1;
	public const int ATTACK = 2;
	public const int DEFENSE = 3;
	public const int SPEED = 4;
	public const int SPECIAL = 5;
	public const int SPECIAL_ATTACK = 8;
	public const int SPECIAL_DEFENSE = 9;

	/**
	 * Get the non battle-only stat ids for this generation.
	 *
	 * @return self[]
	 */
	public static function getByGeneration(GenerationId $generationId) : array
	{
		$generation = $generationId->value();

		if ($generation === 1) {
			return [
				new self(self::HP),
				new self(self::ATTACK),
				new self(self::DEFENSE),
				new self(self::SPECIAL),
				new self(self::SPEED),
			];
		}

		return [
			new self(self::HP),
			new self(self::ATTACK),
			new self(self::DEFENSE),
			new self(self::SPECIAL_ATTACK),
			new self(self::SPECIAL_DEFENSE),
			new self(self::SPEED),
		];
	}
}
