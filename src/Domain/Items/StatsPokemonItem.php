<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Items;

final class StatsPokemonItem
{
	/** @var string $identifier */
	private $identifier;

	/** @var string $name */
	private $name;

	/** @var float $percent */
	private $percent;

	/** @var float $change */
	private $change;

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
	 * Get the item's identifier.
	 *
	 * @return string
	 */
	public function getIdentifier() : string
	{
		return $this->identifier;
	}

	/**
	 * Get the item's name.
	 *
	 * @return string
	 */
	public function getName() : string
	{
		return $this->name;
	}

	/**
	 * Get the item's percent.
	 *
	 * @return float
	 */
	public function getPercent() : float
	{
		return $this->percent;
	}

	/**
	 * Get the item's change.
	 *
	 * @return float
	 */
	public function getChange() : float
	{
		return $this->change;
	}
}
