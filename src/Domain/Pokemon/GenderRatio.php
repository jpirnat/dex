<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Pokemon;

final readonly class GenderRatio
{
	public const int MALE_ONLY = 0;
	public const int FEMALE_ONLY = 254;
	public const int GENDER_UNKNOWN = 255;

	public function __construct(
		private int $value,
	) {}

	public function value(): int
	{
		return $this->value;
	}

	public function getIcon(): string
	{
		return "$this->value.png";
	}

	public function getDescription(): string
	{
		return match ($this->value) {
			0   => '100% male',
			31  => '87.5% male, 12.5% female',
			63  => '75% male, 25% female',
			127 => '50% male, 50% female',
			191 => '25% male, 75% female',
			225 => '12.5% male, 87.5% female',
			254 => '100% female',
			255 => 'Gender Unknown',
			default => '',
		};
	}

	public static function getAll() : array
	{
		return [
			new self(0),
			new self(31),
			new self(63),
			new self(127),
			new self(191),
			new self(225),
			new self(254),
			new self(255),
		];
	}
}
