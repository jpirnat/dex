<?php
declare(strict_types=1);

namespace Jp\Trendalyzer\Importers\Structs;

class Counter
{
	/** @var string $pokemonName */
	protected $pokemonName;

	/** @var float $number1 */
	protected $number1;

	/** @var float $number2 */
	protected $number2;

	/** @var float $number3 */
	protected $number3;

	/** @var float $percentKnockedOut */
	protected $percentKnockedOut;

	/** @var float $percentSwitchedOut */
	protected $percentSwitchedOut;

	/**
	 * Constructor.
	 *
	 * @param string $pokemonName
	 * @param float $number1
	 * @param float $number2
	 * @param float $number3
	 * @param float $percentKnockedOut
	 * @param float $percentSwitchedOut
	 */
	public function __construct(
		string $pokemonName,
		float $number1,
		float $number2,
		float $number3,
		float $percentKnockedOut,
		float $percentSwitchedOut
	) {
		$this->pokemonName = $pokemonName;
		$this->number1 = $number1;
		$this->number2 = $number2;
		$this->number3 = $number3;
		$this->percentKnockedOut = $percentKnockedOut;
		$this->percentSwitchedOut = $percentSwitchedOut;
	}

	/**
	 * Get the Pokémon name.
	 *
	 * @return string
	 */
	public function pokemonName() : string
	{
		return $this->pokemonName;
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
