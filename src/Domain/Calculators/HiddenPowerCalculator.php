<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Calculators;

final readonly class HiddenPowerCalculator
{
	/**
	 * Calculate a Pokémon's Hidden Power type in generation 2.
	 * https://bulbapedia.bulbagarden.net/wiki/Hidden_Power_(move)/Calculation#Type
	 *
	 * @return int The Hidden Power index of the type.
	 */
	public function gen2TypeIndex(int $atk, int $def) : int
	{
		return 4 * $atk % 4 + $def % 4;
	}

	/**
	 * Calculate a Pokémon's Hidden Power type in generations 3 through 8.
	 * https://bulbapedia.bulbagarden.net/wiki/Hidden_Power_(move)/Calculation#Type_2
	 *
	 * @return int The Hidden Power index of the type.
	 */
	public function gen3TypeIndex(
		int $hp,
		int $atk,
		int $def,
		int $spe,
		int $spa,
		int $spd,
	) : int {
		$a = $hp  % 2;
		$b = $atk % 2;
		$c = $def % 2;
		$d = $spe % 2;
		$e = $spa % 2;
		$f = $spd % 2;

		return (int) (($a + 2 * $b + 4 * $c + 8 * $d + 16 * $e + 32 * $f) * 15 / 63);
	}

	/**
	 * Calculate a Pokémon's Hidden Power base power in generation 2.
	 * https://bulbapedia.bulbagarden.net/wiki/Hidden_Power_(move)/Calculation#Power
	 */
	public function gen2Power(
		int $atk,
		int $def,
		int $spe,
		int $spc,
	) : int {
		$v = $spc < 8 ? 0 : 1;
		$w = $spe < 8 ? 0 : 1;
		$x = $def < 8 ? 0 : 1;
		$y = $atk < 8 ? 0 : 1;
		$z = $spc % 4;

		return (int) ((5 * ($v + 2 * $w + 4 * $x + 8 * $y) + $z) / 2 + 31);
	}

	/**
	 * Calculate a Pokémon's Hidden Power base power in generations 3 through 5.
	 * https://bulbapedia.bulbagarden.net/wiki/Hidden_Power_(move)/Calculation#Power_2
	 */
	public function gen3Power(
		int $hp,
		int $atk,
		int $def,
		int $spe,
		int $spa,
		int $spd,
	) : int {
		$u = ($hp  % 4 === 2 || $hp  % 4 === 3) ? 1 : 0;
		$v = ($atk % 4 === 2 || $atk % 4 === 3) ? 1 : 0;
		$w = ($def % 4 === 2 || $def % 4 === 3) ? 1 : 0;
		$x = ($spe % 4 === 2 || $spe % 4 === 3) ? 1 : 0;
		$y = ($spa % 4 === 2 || $spa % 4 === 3) ? 1 : 0;
		$z = ($spd % 4 === 2 || $spd % 4 === 3) ? 1 : 0;

		return (int) ((($u + 2 * $v + 4 * $w + 8 * $x + 16 * $y + 32 * $z) * 40) / 63 + 30);
	}

	/**
	 * Calculate a Pokémon's Hidden Power base power in generations 6 through 7.
	 */
	public function gen6Power() : int
	{
		return 60;
	}
}
