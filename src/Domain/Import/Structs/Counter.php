<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Import\Structs;

final class Counter
{
	public function __construct(
		private string $showdownPokemonName,
		private float $number1,
		private float $number2,
		private float $number3,
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
	 * Get the Pokémon Showdown Pokémon name.
	 */
	public function showdownPokemonName() : string
	{
		return $this->showdownPokemonName;
	}

	/**
	 * Get the number1.
	 */
	public function number1() : float
	{
		return $this->number1;
	}

	/**
	 * Get the number2.
	 */
	public function number2() : float
	{
		return $this->number2;
	}

	/**
	 * Get the number3.
	 */
	public function number3() : float
	{
		return $this->number3;
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
