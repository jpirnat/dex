<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Import\Structs;

final readonly class Counter2
{
	private float $percentKnockedOut;
	private float $percentSwitchedOut;

	public function __construct(
		float $percentKnockedOut,
		float $percentSwitchedOut,
	) {
		// Clamp percent knocked out between 0 and 100.
		if ($percentKnockedOut < 0) {
			$percentKnockedOut = 0;
		}
		if ($percentKnockedOut > 100) {
			$percentKnockedOut = 100;
		}
		$this->percentKnockedOut = $percentKnockedOut;

		// Clamp percent switched out between 0 and 100.
		if ($percentSwitchedOut < 0) {
			$percentSwitchedOut = 0;
		}
		if ($percentSwitchedOut > 100) {
			$percentSwitchedOut = 100;
		}
		$this->percentSwitchedOut = $percentSwitchedOut;
	}

	/**
	 * Get the percent knocked out.
	 */
	public function percentKnockedOut() : float
	{
		return $this->percentKnockedOut;
	}

	/**
	 * Get the percent switched out.
	 */
	public function percentSwitchedOut() : float
	{
		return $this->percentSwitchedOut;
	}
}
