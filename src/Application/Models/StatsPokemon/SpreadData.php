<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\StatsPokemon;

use Jp\Dex\Domain\Stats\StatId;
use Jp\Dex\Domain\Stats\StatValueContainer;

class SpreadData
{
	/** @var string $natureName */
	private $natureName;

	/** @var StatId|null $increasedStatId */
	private $increasedStatId;

	/** @var StatId|null $decreasedStatId */
	private $decreasedStatId;

	/** @var StatValueContainer $evSpread */
	private $evSpread;

	/** @var float $percent */
	private $percent;

	/** @var StatValueContainer $statSpread */
	private $statSpread;

	/**
	 * Constructor.
	 *
	 * @param string $natureName
	 * @param StatId|null $increasedStatId
	 * @param StatId|null $decreasedStatId
	 * @param StatValueContainer $evSpread
	 * @param float $percent
	 * @param StatValueContainer $statSpread
	 */
	public function __construct(
		string $natureName,
		?StatId $increasedStatId,
		?StatId $decreasedStatId,
		StatValueContainer $evSpread,
		float $percent,
		StatValueContainer $statSpread
	) {
		$this->natureName = $natureName;
		$this->increasedStatId = $increasedStatId;
		$this->decreasedStatId = $decreasedStatId;
		$this->evSpread = $evSpread;
		$this->percent = $percent;
		$this->statSpread = $statSpread;
	}

	/**
	 * Get the nature name.
	 *
	 * @return string
	 */
	public function getNatureName() : string
	{
		return $this->natureName;
	}

	/**
	 * Get the nature-increased stat id.
	 *
	 * @return StatId|null
	 */
	public function getIncreasedStatId() : ?StatId
	{
		return $this->increasedStatId;
	}

	/**
	 * Get the nature-decreased stat id.
	 *
	 * @return StatId|null
	 */
	public function getDecreasedStatId() : ?StatId
	{
		return $this->decreasedStatId;
	}

	/**
	 * Get the EV spread.
	 *
	 * @return StatValueContainer
	 */
	public function getEvSpread() : StatValueContainer
	{
		return $this->evSpread;
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
	 * Get the stat spread.
	 *
	 * @return StatValueContainer
	 */
	public function getStatSpread() : StatValueContainer
	{
		return $this->statSpread;
	}
}
