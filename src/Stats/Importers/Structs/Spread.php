<?php
declare(strict_types=1);

namespace Jp\Dex\Stats\Importers\Structs;

class Spread
{
	/** @var string $showdownNatureName */
	protected $showdownNatureName;

	/** @var int $hp */
	protected $hp;

	/** @var int $atk */
	protected $atk;

	/** @var int $def */
	protected $def;

	/** @var int $spa */
	protected $spa;

	/** @var int $spd */
	protected $spd;

	/** @var int $spe */
	protected $spe;

	/** @var float $percent */
	protected $percent;

	/**
	 * Constructor.
	 *
	 * @param string $showdownNatureName
	 * @param int $hp
	 * @param int $atk
	 * @param int $def
	 * @param int $spa
	 * @param int $spd
	 * @param int $spe
	 * @param float $percent
	 */
	public function __construct(
		string $showdownNatureName,
		int $hp,
		int $atk,
		int $def,
		int $spa,
		int $spd,
		int $spe,
		float $percent
	) {
		$this->showdownNatureName = $showdownNatureName;
		$this->hp = $hp;
		$this->atk = $atk;
		$this->def = $def;
		$this->spa = $spa;
		$this->spd = $spd;
		$this->spe = $spe;
		$this->percent = $percent;
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
