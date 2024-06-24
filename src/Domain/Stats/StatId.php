<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats;

use Jp\Dex\Domain\EntityId;
use Jp\Dex\Domain\Versions\GenerationId;
use Jp\Dex\Domain\Versions\VersionGroupId;

final class StatId extends EntityId
{
	public const HP = 1;
	public const ATTACK = 2;
	public const DEFENSE = 3;
	public const SPEED = 4;
	public const SPECIAL = 5;
	public const SPECIAL_ATTACK = 8;
	public const SPECIAL_DEFENSE = 9;

	/**
	 * Get the non battle-only stat ids for this generation.
	 *
	 * @return self[]
	 */
	public static function getByVersionGroup(VersionGroupId $versionGroupId) : array
	{
		if ($versionGroupId->value() <= VersionGroupId::YELLOW) {
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
