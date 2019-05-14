<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Teammates;

class StatsPokemonTeammate
{
	/** @var string $icon */
	private $icon;

	/** @var string $identifier */
	private $identifier;

	/** @var string $name */
	private $name;

	/** @var float $percent */
	private $percent;

	/**
	 * Constructor.
	 *
	 * @param string $icon
	 * @param string $identifier
	 * @param string $name
	 * @param float $percent
	 */
	public function __construct(
		string $icon,
		string $identifier,
		string $name,
		float $percent
	) {
		$this->icon = $icon;
		$this->identifier = $identifier;
		$this->name = $name;
		$this->percent = $percent;
	}

	/**
	 * Get the teammate's icon.
	 *
	 * @return string
	 */
	public function getIcon() : string
	{
		return $this->icon;
	}

	/**
	 * Get the teammate's identifier.
	 *
	 * @return string
	 */
	public function getIdentifier() : string
	{
		return $this->identifier;
	}

	/**
	 * Get the teammate's name.
	 *
	 * @return string
	 */
	public function getName() : string
	{
		return $this->name;
	}

	/**
	 * Get the teammate's percent.
	 *
	 * @return float
	 */
	public function getPercent() : float
	{
		return $this->percent;
	}
}
