<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\MovesetPokemonMonth;

use Jp\Dex\Domain\Stats\StatValueContainer;

class SpreadData
{
	/** @var string $natureName */
	private $natureName;

	/** @var StatValueContainer $natureModifiers */
	private $natureModifiers;

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
	 * @param StatValueContainer $natureModifiers
	 * @param StatValueContainer $evSpread
	 * @param float $percent
	 * @param StatValueContainer $statSpread
	 */
	public function __construct(
		string $natureName,
		StatValueContainer $natureModifiers,
		StatValueContainer $evSpread,
		float $percent,
		StatValueContainer $statSpread
	) {
		$this->natureName = $natureName;
		$this->natureModifiers = $natureModifiers;
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
	 * Get the nature modifiers.
	 *
	 * @return StatValueContainer
	 */
	public function getNatureModifiers() : StatValueContainer
	{
		return $this->natureModifiers;
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
