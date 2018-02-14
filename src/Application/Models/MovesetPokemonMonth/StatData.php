<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\MovesetPokemonMonth;

class StatData
{
	/** @var string $statName */
	private $statName;

	/** @var int $baseStat */
	private $baseStat;

	/**
	 * Constructor.
	 *
	 * @param string $statName
	 * @param int $baseStat
	 */
	public function __construct(
		string $statName,
		int $baseStat
	) {
		$this->statName = $statName;
		$this->baseStat = $baseStat;
	}

	/**
	 * Get the stat name.
	 *
	 * @return string
	 */
	public function getStatName() : string
	{
		return $this->statName;
	}

	/**
	 * Get the base stat.
	 *
	 * @return int
	 */
	public function getBaseStat() : int
	{
		return $this->baseStat;
	}
}
