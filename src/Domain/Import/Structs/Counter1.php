<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Import\Structs;

final class Counter1
{
	public function __construct(
		private string $showdownPokemonName,
		private float $number1,
		private float $number2,
		private float $number3,
	) {}

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
}
