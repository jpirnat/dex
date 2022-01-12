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
	 */
	public function showdownNatureName() : string
	{
		return $this->showdownNatureName;
	}

	/**
	 * Get the HP EVs.
	 */
	public function hp() : int
	{
		return $this->hp;
	}

	/**
	 * Get the Attack EVs.
	 */
	public function atk() : int
	{
		return $this->atk;
	}

	/**
	 * Get the Defense EVs.
	 */
	public function def() : int
	{
		return $this->def;
	}

	/**
	 * Get the Special Attack EVs.
	 */
	public function spa() : int
	{
		return $this->spa;
	}

	/**
	 * Get the Special Defense EVs.
	 */
	public function spd() : int
	{
		return $this->spd;
	}

	/**
	 * Get the Speed EVs.
	 */
	public function spe() : int
	{
		return $this->spe;
	}

	/**
	 * Get the percent.
	 */
	public function percent() : float
	{
		return $this->percent;
	}
}
