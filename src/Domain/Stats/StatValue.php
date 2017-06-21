<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats;

class StatValue
{
	/** @var StatId $statId */
	private $statId;

	/** @var float $value */
	private $value;

	/**
	 * Constructor.
	 *
	 * @param StatId $statId
	 * @param float $value
	 */
	public function __construct(StatId $statId, float $value)
	{
		$this->statId = $statId;
		$this->value = $value;
	}

	/**
	 * Get the stat id.
	 *
	 * @return StatId
	 */
	public function getStatId() : StatId
	{
		return $this->statId;
	}

	/**
	 * Get the value.
	 *
	 * @return float
	 */
	public function getValue() : float
	{
		return $this->value;
	}
}
