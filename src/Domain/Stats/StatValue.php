<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats;

class StatValue
{
	/** @var StatId $statId */
	private $statId;

	/** @var int $value */
	private $value;

	/**
	 * Constructor.
	 *
	 * @param StatId $statId
	 * @param int $value
	 */
	public function __construct(StatId $statId, int $value)
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
	 * @return int
	 */
	public function getValue() : int
	{
		return $this->value;
	}
}
