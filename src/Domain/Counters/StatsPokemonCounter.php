<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Counters;

class StatsPokemonCounter
{
	/** @var string $icon */
	private $icon;

	/** @var string $identifier */
	private $identifier;

	/** @var string $name */
	private $name;

	/** @var float $score */
	private $score;

	/** @var float $percent */
	private $percent;

	/** @var float $standardDeviation */
	private $standardDeviation;

	/** @var float $percentKnockedOut */
	private $percentKnockedOut;

	/** @var float $percentSwitchedOut */
	private $percentSwitchedOut;

	/**
	 * Constructor.
	 *
	 * @param string $icon
	 * @param string $identifier
	 * @param string $name
	 * @param float $score
	 * @param float $percent
	 * @param float $standardDeviation
	 * @param float $percentKnockedOut
	 * @param float $percentSwitchedOut
	 */
	public function __construct(
		string $icon,
		string $identifier,
		string $name,
		float $score,
		float $percent,
		float $standardDeviation,
		float $percentKnockedOut,
		float $percentSwitchedOut
	) {
		$this->icon = $icon;
		$this->identifier = $identifier;
		$this->name = $name;
		$this->score = $score;
		$this->percent = $percent;
		$this->standardDeviation = $standardDeviation;
		$this->percentKnockedOut = $percentKnockedOut;
		$this->percentSwitchedOut = $percentSwitchedOut;
	}

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
