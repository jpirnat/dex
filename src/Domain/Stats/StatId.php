<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats;

use Jp\Dex\Domain\EntityId;
use Jp\Dex\Domain\Versions\GenerationId;

final class StatId extends EntityId
{
	/** @var int $HP */
	public const HP = 1;

	/** @var int $ATTACK */
	public const ATTACK = 2;

	/** @var int $DEFENSE */
	public const DEFENSE = 3;

	/** @var int $SPEED */
	public const SPEED = 4;

	/** @var int SPECIAL */
	public const SPECIAL = 5;

	/** @var int $SPECIAL_ATTACK */
	public const SPECIAL_ATTACK = 8;

	/** @var int $SPECIAL_DEFENSE */
	public const SPECIAL_DEFENSE = 9;

	/**
	 * Get the non battle-only stat ids for this generation.
	 *
	 * @param GenerationId $generationId
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

	/**
	 * Get the mapping of stat ids to object keys for json stat objects.
	 *
	 * return string[]
	 */
	public static function getIdsToIdentifiers() : array
	{
		return [
			StatId::HP => 'hp',
			StatId::ATTACK => 'atk',
			StatId::DEFENSE => 'def',
			StatId::SPEED => 'spe',
			StatId::SPECIAL => 'spc',
			StatId::SPECIAL_ATTACK => 'spa',
			StatId::SPECIAL_DEFENSE => 'spd',
		];
	}
}
