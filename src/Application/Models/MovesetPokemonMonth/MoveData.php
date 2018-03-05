<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\MovesetPokemonMonth;

class MoveData
{
	/** @var string $moveName */
	private $moveName;

	/** @var string $moveIdentifier */
	private $moveIdentifier;

	/** @var float $percent */
	private $percent;

	/** @var float $change */
	private $change;

	/**
	 * Constructor.
	 *
	 * @param string $moveName
	 * @param string $moveIdentifier
	 * @param float $percent
	 * @param float $change
	 */
	public function __construct(
		string $moveName,
		string $moveIdentifier,
		float $percent,
		float $change
	) {
		$this->moveName = $moveName;
		$this->moveIdentifier = $moveIdentifier;
		$this->percent = $percent;
		$this->change = $change;
	}

	/**
	 * Get the move name.
	 *
	 * @return string
	 */
	public function getMoveName() : string
	{
		return $this->moveName;
	}

	/**
	 * Get the move identifier.
	 *
	 * @return string
	 */
	public function getMoveIdentifier() : string
	{
		return $this->moveIdentifier;
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
