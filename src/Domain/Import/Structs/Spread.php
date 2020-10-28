<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Import\Structs;

final class Spread
{
	public function __construct(
		private string $showdownNatureName,
		private int $hp,
		private int $atk,
		private int $def,
		private int $spa,
		private int $spd,
		private int $spe,
		private float $percent,
	) {
		// Clamp percent between 0 and 100.
		if ($this->percent < 0) {
			$this->percent = 0;
		}
		if ($this->percent > 100) {
			$this->percent = 100;
		}
	}

	/**
	 * Get the Pokémon Showdown nature name.
	 *
	 * @return string
	 */
	public function showdownNatureName() : string
	{
		return $this->showdownNatureName;
	}

	/**
	 * Get the HP EVs.
	 *
	 * @return int
	 */
	public function hp() : int
	{
		return $this->hp;
	}

	/**
	 * Get the Attack EVs.
	 *
	 * @return int
	 */
	public function atk() : int
	{
		return $this->atk;
	}

	/**
	 * Get the Defense EVs.
	 *
	 * @return int
	 */
	public function def() : int
	{
		return $this->def;
	}

	/**
	 * Get the Special Attack EVs.
	 *
	 * @return int
	 */
	public function spa() : int
	{
		return $this->spa;
	}

	/**
	 * Get the Special Defense EVs.
	 *
	 * @return int
	 */
	public function spd() : int
	{
		return $this->spd;
	}

	/**
	 * Get the Speed EVs.
	 *
	 * @return int
	 */
	public function spe() : int
	{
		return $this->spe;
	}

	/**
	 * Get the percent.
	 *
	 * @return float
	 */
	public function percent() : float
	{
		return $this->percent;
	}
}
