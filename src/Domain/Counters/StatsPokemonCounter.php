<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Counters;

final class StatsPokemonCounter
{
	public function __construct(
		private string $icon,
		private string $identifier,
		private string $name,
		private float $score,
		private float $percent,
		private float $standardDeviation,
		private float $percentKnockedOut,
		private float $percentSwitchedOut,
	) {}

	/**
	 * Get the counter's icon.
	 */
	public function getIcon() : string
	{
		return $this->icon;
	}

	/**
	 * Get the counter's identifier.
	 */
	public function getIdentifier() : string
	{
		return $this->identifier;
	}

	/**
	 * Get the counter's name.
	 */
	public function getName() : string
	{
		return $this->name;
	}

	/**
	 * Get the counter's score.
	 */
	public function getScore() : float
	{
		return $this->score;
	}

	/**
	 * Get the counter's percent.
	 */
	public function getPercent() : float
	{
		return $this->percent;
	}

	/**
	 * Get the counter's standard deviation.
	 */
	public function getStandardDeviation() : float
	{
		return $this->standardDeviation;
	}

	/**
	 * Get the counter's percent knocked out.
	 */
	public function getPercentKnockedOut() : float
	{
		return $this->percentKnockedOut;
	}

	/**
	 * Get the counter's percent switched out.
	 */
	public function getPercentSwitchedOut() : float
	{
		return $this->percentSwitchedOut;
	}
}
