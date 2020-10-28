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
	 *
	 * @return string
	 */
	public function getIcon() : string
	{
		return $this->icon;
	}

	/**
	 * Get the counter's identifier.
	 *
	 * @return string
	 */
	public function getIdentifier() : string
	{
		return $this->identifier;
	}

	/**
	 * Get the counter's name.
	 *
	 * @return string
	 */
	public function getName() : string
	{
		return $this->name;
	}

	/**
	 * Get the counter's score.
	 *
	 * @return float
	 */
	public function getScore() : float
	{
		return $this->score;
	}

	/**
	 * Get the counter's percent.
	 *
	 * @return float
	 */
	public function getPercent() : float
	{
		return $this->percent;
	}

	/**
	 * Get the counter's standard deviation.
	 *
	 * @return float
	 */
	public function getStandardDeviation() : float
	{
		return $this->standardDeviation;
	}

	/**
	 * Get the counter's percent knocked out.
	 *
	 * @return float
	 */
	public function getPercentKnockedOut() : float
	{
		return $this->percentKnockedOut;
	}

	/**
	 * Get the counter's percent switched out.
	 *
	 * @return float
	 */
	public function getPercentSwitchedOut() : float
	{
		return $this->percentSwitchedOut;
	}
}
