<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\MovesetPokemonMonth;

class TeammateData
{
	/** @var string $pokemonName */
	private $pokemonName;

	/** @var bool $movesetDataExists */
	private $movesetDataExists;

	/** @var string $pokemonIdentifier */
	private $pokemonIdentifier;

	/** @var string $formIcon */
	private $formIcon;

	/** @var float $percent */
	private $percent;

	/**
	 * Constructor.
	 *
	 * @param string $pokemonName
	 * @param bool $movesetDataExists
	 * @param string $pokemonIdentifier
	 * @param string $formIcon
	 * @param float $percent
	 */
	public function __construct(
		string $pokemonName,
		bool $movesetDataExists,
		string $pokemonIdentifier,
		string $formIcon,
		float $percent
	) {
		$this->pokemonName = $pokemonName;
		$this->movesetDataExists = $movesetDataExists;
		$this->pokemonIdentifier = $pokemonIdentifier;
		$this->formIcon = $formIcon;
		$this->percent = $percent;
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
	 * Get the percent.
	 *
	 * @return float
	 */
	public function getPercent() : float
	{
		return $this->percent;
	}
}
