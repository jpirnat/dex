<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\MovesetPokemonMonth;

class MoveData
{
	/** @var string $moveName */
	private $moveName;

	/** @var float $percent */
	private $percent;

	/**
	 * Constructor.
	 *
	 * @param string $moveName
	 * @param float $percent
	 */
	public function __construct(
		string $moveName,
		float $percent
	) {
		$this->moveName = $moveName;
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
	 * Get the percent.
	 *
	 * @return float
	 */
	public function getPercent() : float
	{
		return $this->percent;
	}
}
