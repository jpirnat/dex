<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats;

class StatValueContainer
{
	/** @var StatValue[] $statValues */
	private $statValues = [];

	/**
	 * Add a stat value to the container.
	 *
	 * @param StatValue $statValue
	 *
	 * @return void
	 */
	public function add(StatValue $statValue) : void
	{
		$this->statValues[$statValue->getStatId()->value()] = $statValue;
	}

	/**
	 * Get a stat value from the container.
	 *
	 * @param StatId $statId
	 *
	 * @throws StatNotFoundException if the container does not have a stat value
	 *     for this stat id.
	 *
	 * @return StatValue
	 */
	public function get(StatId $statId) : StatValue
	{
		if (!isset($this->statValues[$statId->value()])) {
			throw new StatNotFoundException(
				'Stat value container does not have stat value for stat id '
				. $statId->value() . '.'
			);
		}

		return $this->statValues[$statId->value()];
	}

	/**
	 * Get all stat values from the container.
	 *
	 * @return StatValue[]
	 */
	public function getAll() : array
	{
		return $this->statValues;
	}
}
