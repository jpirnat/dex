<?php
declare(strict_types=1);

namespace Jp\Dex\Stats\Importers\Structs;

class Counter2
{
	/** @var float $percentKnockedOut */
	protected $percentKnockedOut;

	/** @var float $percentSwitchedOut */
	protected $percentSwitchedOut;

	/**
	 * Constructor.
	 *
	 * @param float $percentKnockedOut
	 * @param float $percentSwitchedOut
	 */
	public function __construct(
		float $percentKnockedOut,
		float $percentSwitchedOut
	) {
		$this->percentKnockedOut = $percentKnockedOut;
		$this->percentSwitchedOut = $percentSwitchedOut;
	}

	/**
	 * Get the percent knocked out.
	 *
	 * @return float
	 */
	public function percentKnockedOut() : float
	{
		return $this->percentKnockedOut;
	}

	/**
	 * Get the percent switched out.
	 *
	 * @return float
	 */
	public function percentSwitchedOut() : float
	{
		return $this->percentSwitchedOut;
	}
}
