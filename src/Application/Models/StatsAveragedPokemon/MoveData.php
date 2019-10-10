<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\StatsAveragedPokemon;

final class MoveData
{
	/** @var string $moveName */
	private $moveName;

	/** @var string $moveIdentifier */
	private $moveIdentifier;

	/** @var float $percent */
	private $percent;

	/**
	 * Constructor.
	 *
	 * @param string $moveName
	 * @param string $moveIdentifier
	 * @param float $percent
	 */
	public function __construct(
		string $moveName,
		string $moveIdentifier,
		float $percent
	) {
		$this->moveName = $moveName;
		$this->moveIdentifier = $moveIdentifier;
		$this->percent = $percent;
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
}
