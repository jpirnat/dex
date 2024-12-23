<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats;

final class StatValueContainer
{
	/** @var StatValue[] $statValues */
	private(set) array $statValues = [];

	/**
	 * Constructor.
	 *
	 * @param StatValue[] $statValues
	 */
	public function __construct(?array $statValues = [])
	{
		foreach ($statValues as $statValue) {
			$this->add($statValue);
		}
	}

	/**
	 * Add a stat value to the container.
	 */
	public function add(StatValue $statValue) : void
	{
		$statId = $statValue->statId->value();
		$this->statValues[$statId] = $statValue;
	}

	/**
	 * Get a stat value from the container.
	 *
	 * @throws StatNotFoundException if the container does not have a stat value
	 *     for this stat id.
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
}
