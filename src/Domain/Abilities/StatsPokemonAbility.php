<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Abilities;

final class StatsPokemonAbility
{
	private string $identifier;
	private string $name;
	private float $percent;
	private float $change;

	/**
	 * Constructor.
	 *
	 * @param string $identifier
	 * @param string $name
	 * @param float $percent
	 * @param float $change
	 */
	public function __construct(
		string $identifier,
		string $name,
		float $percent,
		float $change
	) {
		$this->identifier = $identifier;
		$this->name = $name;
		$this->percent = $percent;
		$this->change = $change;
	}

	/**
	 * Get the ability's identifier.
	 *
	 * @return string
	 */
	public function getIdentifier() : string
	{
		return $this->identifier;
	}

	/**
	 * Get the ability's name.
	 *
	 * @return string
	 */
	public function getName() : string
	{
		return $this->name;
	}

	/**
	 * Get the ability's percent.
	 *
	 * @return float
	 */
	public function getPercent() : float
	{
		return $this->percent;
	}

	/**
	 * Get the ability's change.
	 *
	 * @return float
	 */
	public function getChange() : float
	{
		return $this->change;
	}
}
