<?php

class DamageCalculator
{
	/**
	 * Calculate 
	 *
	 * @param int $level
	 * @param int $attack
	 * @param int $defense
	 * @param int $base
	 *
	 * @return int
	 */
	public function calculate(
		int $level,
		int $attack,
		int $defense,
		int $base
	) : int {
		$modifier = 1;

		$damage = ((2 * $level + 10) / 250 * ($attack / $defense) * $base + 2) * $modifier;
	}
}

// http://bulbapedia.bulbagarden.net/wiki/Damage#Damage_formula
