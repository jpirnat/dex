<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Import\Structs;

class Counter
{
	/** @var string $showdownPokemonName */
	private $showdownPokemonName;

	/** @var float $number1 */
	private $number1;

	/** @var float $number2 */
	private $number2;

	/** @var float $number3 */
	private $number3;

	/** @var float $percentKnockedOut */
	private $percentKnockedOut;

	/** @var float $percentSwitchedOut */
	private $percentSwitchedOut;

	/**
	 * Constructor.
	 *
	 * @param string $showdownPokemonName
	 * @param float $number1
	 * @param float $number2
	 * @param float $number3
	 * @param float $percentKnockedOut
	 * @param float $percentSwitchedOut
	 */
	public function __construct(
		string $showdownPokemonName,
		float $number1,
		float $number2,
		float $number3,
		float $percentKnockedOut,
		float $percentSwitchedOut
	) {
		// Clamp percent knocked out between 0 and 100.
		if ($percentKnockedOut < 0) {
			$percentKnockedOut = 0;
		}
		if ($percentKnockedOut > 100) {
			$percentKnockedOut = 100;
		}

		// Clamp percent switched out between 0 and 100.
		if ($percentSwitchedOut < 0) {
			$percentSwitchedOut = 0;
		}
		if ($percentSwitchedOut > 100) {
			$percentSwitchedOut = 100;
		}

		$this->showdownPokemonName = $showdownPokemonName;
		$this->number1 = $number1;
		$this->number2 = $number2;
		$this->number3 = $number3;
		$this->percentKnockedOut = $percentKnockedOut;
		$this->percentSwitchedOut = $percentSwitchedOut;
	}

	/**
	 * Get the Pokémon Showdown Pokémon name.
	 *
	 * @return string
	 */
	public function showdownPokemonName() : string
	{
		return $this->showdownPokemonName;
	}

	/**
	 * Get the number1.
	 *
	 * @return float
	 */
	public function number1() : float
	{
		return $this->number1;
	}

	/**
	 * Get the number2.
	 *
	 * @return float
	 */
	public function number2() : float
	{
		return $this->number2;
	}

	/**
	 * Get the number3.
	 *
	 * @return float
	 */
	public function number3() : float
	{
		return $this->number3;
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
