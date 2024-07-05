<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Evolutions;

use Jp\Dex\Domain\Versions\VersionGroupId;

final readonly class AlcremieSpinType
{
	private const CLOCKWISE_LT_5_DAY = 1;
	private const COUNTERCLOCKWISE_LT_5_DAY = 2;
	private const CLOCKWISE_LT_5_NIGHT = 3;
	private const COUNTERCLOCKWISE_GT_5_NIGHT = 4;
	private const CLOCKWISE_GT_5_NIGHT = 5;
	private const COUNTERCLOCKWISE_LT_5_NIGHT = 6;
	private const COUNTERCLOCKWISE_GT_5_DAY = 7;
	private const CLOCKWISE_GT_5_DAY = 8;
	private const COUNTERCLOCKWISE_GT_10_EVENING = 9;

	public function __construct(
		private int $value,
	) {}

	public function getDirection() : string
	{
		return match ($this->value) {
			self::CLOCKWISE_LT_5_DAY,
			self::CLOCKWISE_LT_5_NIGHT,
			self::CLOCKWISE_GT_5_NIGHT,
			self::CLOCKWISE_GT_5_DAY,
				=> 'clockwise',
			self::COUNTERCLOCKWISE_LT_5_DAY,
			self::COUNTERCLOCKWISE_GT_5_NIGHT,
			self::COUNTERCLOCKWISE_LT_5_NIGHT,
			self::COUNTERCLOCKWISE_GT_5_DAY,
			self::COUNTERCLOCKWISE_GT_10_EVENING,
				=>  'counterclockwise',
		};
	}

	public function getDuration() : string
	{
		return match ($this->value) {
			self::CLOCKWISE_LT_5_DAY,
			self::COUNTERCLOCKWISE_LT_5_DAY,
			self::CLOCKWISE_LT_5_NIGHT,
			self::COUNTERCLOCKWISE_LT_5_NIGHT,
				=> 'for less than 5 seconds',
			self::COUNTERCLOCKWISE_GT_5_NIGHT,
			self::CLOCKWISE_GT_5_NIGHT,
			self::COUNTERCLOCKWISE_GT_5_DAY,
			self::CLOCKWISE_GT_5_DAY,
				=> 'for more than 5 seconds',
			self::COUNTERCLOCKWISE_GT_10_EVENING,
				=> 'for more than 10 seconds',
		};
	}

	public function getTimeOfDay(VersionGroupId $versionGroupId) : string
	{
		return match ($this->value) {
			self::CLOCKWISE_LT_5_DAY,
			self::COUNTERCLOCKWISE_LT_5_DAY,
			self::COUNTERCLOCKWISE_GT_5_DAY,
			self::CLOCKWISE_GT_5_DAY,
				=> 'during the day',
			self::CLOCKWISE_LT_5_NIGHT,
			self::COUNTERCLOCKWISE_GT_5_NIGHT,
			self::CLOCKWISE_GT_5_NIGHT,
			self::COUNTERCLOCKWISE_LT_5_NIGHT,
				=> 'during the night',
			self::COUNTERCLOCKWISE_GT_10_EVENING,
				=> EvolutionFormatter::getEveningText($versionGroupId),
		};
	}
}
