<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Calculators;

class StatCalculator
{
	/**
	 * Calculate a Pokémon's HP stat in generations 1 or 2.
	 *
	 * @param int $base
	 * @param int $iv
	 * @param int $ev
	 * @param int $level
	 *
	 * @return int
	 */
	public function hp1(int $base, int $iv, int $ev, int $level)
	{
		return floor(((($base + $iv) * 2 + floor(ceil(sqrt($ev)) / 4)) * $level) / 100) + $level + 10;
	}

	/**
	 * Calculate a Pokémon's non-HP stat in generations 1 or 2.
	 *
	 * @param int $base
	 * @param int $iv
	 * @param int $ev
	 * @param int $level
	 *
	 * @return int
	 */
	public function other1(int $base, int $iv, int $ev, int $level)
	{
		return floor(((($base + $iv) * 2 + floor(ceil(sqrt($ev))/4)) * $level) / 100) + 5;
	}

	/**
	 * Calculate a Pokémon's HP stat in generations 3 and above.
	 *
	 * @param int $base
	 * @param int $iv
	 * @param int $ev
	 * @param int $level
	 *
	 * @return int
	 */
	public function hp3(int $base, int $iv, int $ev, int $level)
	{
		return floor(((2 * $base + $iv + floor($ev / 4)) * $level) / 100) + $level + 10;
	}

	/**
	 * Calculate a Pokémon's non-HP stat in generations 3 and above.
	 *
	 * @param int $base
	 * @param int $iv
	 * @param int $ev
	 * @param int $level
	 * @param float $nature
	 *
	 * @return int
	 */
	public function other3(int $base, int $iv, int $ev, int $level, float $nature)
	{
		return floor((floor(((2 * $base + $iv + floor($ev / 4)) * $level) / 100) + 5) * $nature);
	}
}
