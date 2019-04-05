<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\StatsPokemon;

class CounterData
{
	/** @var string $pokemonName */
	private $pokemonName;

	/** @var bool $movesetDataExists */
	private $movesetDataExists;

	/** @var string $pokemonIdentifier */
	private $pokemonIdentifier;

	/** @var string $formIcon */
	private $formIcon;

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
	 * @param string $pokemonName
	 * @param bool $movesetDataExists
	 * @param string $pokemonIdentifier
	 * @param string $formIcon
	 * @param float $number1
	 * @param float $number2
	 * @param float $number3
	 * @param float $percentKnockedOut
	 * @param float $percentSwitchedOut
	 */
	public function __construct(
		string $pokemonName,
		bool $movesetDataExists,
		string $pokemonIdentifier,
		string $formIcon,
		float $number1,
		float $number2,
		float $number3,
		float $percentKnockedOut,
		float $percentSwitchedOut
	) {
		$this->pokemonName = $pokemonName;
		$this->movesetDataExists = $movesetDataExists;
		$this->pokemonIdentifier = $pokemonIdentifier;
		$this->formIcon = $formIcon;
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
	public function getPokemonName() : string
	{
		return $this->pokemonName;
	}

	/**
	 * Get whether moveset data exists.
	 *
	 * @return bool
	 */
	public function doesMovesetDataExist() : bool
	{
		return $this->movesetDataExists;
	}

	/**
	 * Get the Pokémon identifier.
	 *
	 * @return string
	 */
	public function getPokemonIdentifier() : string
	{
		return $this->pokemonIdentifier;
	}

	/**
	 * Get the form icon.
	 *
	 * @return string
	 */
	public function getFormIcon() : string
	{
		return $this->formIcon;
	}

	/**
	 * Get the number1.
	 *
	 * @return float
	 */
	public function getNumber1() : float
	{
		return $this->number1;
	}

	/**
	 * Get the number2.
	 *
	 * @return float
	 */
	public function getNumber2() : float
	{
		return $this->number2;
	}

	/**
	 * Get the number3.
	 *
	 * @return float
	 */
	public function getNumber3() : float
	{
		return $this->number3;
	}

	/**
	 * Get the percent knocked out.
	 *
	 * @return float
	 */
	public function getPercentKnockedOut() : float
	{
		return $this->percentKnockedOut;
	}

	/**
	 * Get the percent switched out.
	 *
	 * @return float
	 */
	public function getPercentSwitchedOut() : float
	{
		return $this->percentSwitchedOut;
	}
}
