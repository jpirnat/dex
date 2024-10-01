<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Pokemon;

final readonly class GenderRatio
{
	public function __construct(
		private int $value,
	) {}

	public function getIcon(): string
	{
		return "$this->value.png";
	}

	public function getDescription(): string
	{
		return match ($this->value) {
			-1 => 'Genderless',
			0 => '100% male',
			1 => '87.5% male, 12.5% female',
			2 => '75% male, 25% female',
			4 => '50% male, 50% female',
			6 => '25% male, 75% female',
			7 => '12.5% male, 87.5% female',
			8 => '100% female',
			default => '',
		};
	}
}
