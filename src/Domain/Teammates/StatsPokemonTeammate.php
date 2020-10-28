<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Teammates;

final class StatsPokemonTeammate
{
	public function __construct(
		private string $icon,
		private string $identifier,
		private string $name,
		private float $percent,
	) {}

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
