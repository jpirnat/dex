<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\MovesetPokemonMonth;

class AbilityData
{
	/** @var string $abilityName */
	private $abilityName;

	/** @var float $percent */
	private $percent;

	/** @var float $change */
	private $change;

	/**
	 * Constructor.
	 *
	 * @param string $abilityName
	 * @param float $percent
	 * @param float $change
	 */
	public function __construct(
		string $abilityName,
		float $percent,
		float $change
	) {
		$this->abilityName = $abilityName;
		$this->percent = $percent;
		$this->change = $change;
	}

	/**
	 * Get the ability name.
	 *
	 * @return string
	 */
	public function getAbilityName() : string
	{
		return $this->abilityName;
	}

	/**
	 * Get the percent.
	 *
	 * @return float
	 */
	public function getPercent() : float
	{
		return $this->percent;
	}

	/**
	 * Get the change.
	 *
	 * @return float
	 */
	public function getChange() : float
	{
		return $this->change;
	}
}
