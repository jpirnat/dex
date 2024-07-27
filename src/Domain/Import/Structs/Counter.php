<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Import\Structs;

final readonly class Counter
{
	private float $percentKnockedOut;
	private float $percentSwitchedOut;

	public function __construct(
		private string $showdownPokemonName,
		private float $number1,
		private float $number2,
		private float $number3,
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
