<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Import\Structs;

class Counter1
{
	/** @var string $showdownPokemonName */
	private $showdownPokemonName;

	/** @var float $number1 */
	private $number1;

	/** @var float $number2 */
	private $number2;

	/** @var float $number3 */
	private $number3;

	/**
	 * Constructor.
	 *
	 * @param string $showdownPokemonName
	 * @param float $number1
	 * @param float $number2
	 * @param float $number3
	 */
	public function __construct(
		string $showdownPokemonName,
		float $number1,
		float $number2,
		float $number3
	) {
		$this->showdownPokemonName = $showdownPokemonName;
		$this->number1 = $number1;
		$this->number2 = $number2;
		$this->number3 = $number3;
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
}
