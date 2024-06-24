<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Import\Structs;

final readonly class Counter2
{
	public function __construct(
		private float $percentKnockedOut,
		private float $percentSwitchedOut,
	) {
		// Clamp percent knocked out between 0 and 100.
		if ($this->percentKnockedOut < 0) {
			$this->percentKnockedOut = 0;
		}
		if ($this->percentKnockedOut > 100) {
			$this->percentKnockedOut = 100;
		}

		// Clamp percent switched out between 0 and 100.
		if ($this->percentSwitchedOut < 0) {
			$this->percentSwitchedOut = 0;
		}
		if ($this->percentSwitchedOut > 100) {
			$this->percentSwitchedOut = 100;
		}
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
