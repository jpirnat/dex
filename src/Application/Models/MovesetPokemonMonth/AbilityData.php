<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\MovesetPokemonMonth;

class AbilityData
{
	/** @var string $abilityName */
	private $abilityName;

	/** @var float $percent */
	private $percent;

	/**
	 * Constructor.
	 *
	 * @param string $abilityName
	 * @param float $percent
	 */
	public function __construct(
		string $abilityName,
		float $percent
	) {
		$this->abilityName = $abilityName;
		$this->percent = $percent;
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
}
