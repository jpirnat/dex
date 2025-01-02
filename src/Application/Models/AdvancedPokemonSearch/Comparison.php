<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\AdvancedPokemonSearch;

enum Comparison: string
{
	case Equals0 = 'eq0';
	case EqualsQuarter = 'eqq';
	case EqualsHalf = 'eqh';
	case Equals1 = 'eq1';
	case Equals2 = 'eq2';
	case Equals4 = 'eq4';
	case LessThan1 = 'lt1';
	case GreaterThan1 = 'gt1';

	public function evaluate(float $multiplier): bool
	{
		return match ($this) {
			self::Equals0 => $multiplier === 0.0,
			self::EqualsQuarter => $multiplier === 0.25,
			self::EqualsHalf => $multiplier === 0.5,
			self::Equals1 => $multiplier === 1.0,
			self::Equals2 => $multiplier === 2.0,
			self::Equals4 => $multiplier === 4.0,
			self::LessThan1 => $multiplier < 1.0,
			self::GreaterThan1 => $multiplier > 1.0,
		};
	}
}
