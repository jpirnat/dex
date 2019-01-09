<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\BreedingChains;

class BreedingChainRecord
{
	/** @var string $formIcon */
	private $formIcon;

	/** @var string $generationIdentifier */
	private $generationIdentifier;

	/** @var string $pokemonIdentifier */
	private $pokemonIdentifier;

	/** @var string $pokemonName */
	private $pokemonName;

	/** @var string $versionGroupIcon */
	private $versionGroupIcon;

	/** @var string[] $eggGroupNames */
	private $eggGroupNames;

	/** @var int $baseEggCycles */
	private $baseEggCycles;

	/** @var string $genderRatioIcon */
	private $genderRatioIcon;

	/** @var string $moveMethod */
	private $moveMethod;

	/**
	 * Constructor.
	 *
	 * @param string $formIcon
	 * @param string $generationIdentifier
	 * @param string $pokemonIdentifier
	 * @param string $pokemonName
	 * @param string $versionGroupIcon
	 * @param string[] $eggGroupNames
	 * @param int $baseEggCycles
	 * @param string $genderRatioIcon
	 * @param string $moveMethod
	 */
	public function __construct(
		string $formIcon,
		string $generationIdentifier,
		string $pokemonIdentifier,
		string $pokemonName,
		string $versionGroupIcon,
		array $eggGroupNames,
		int $baseEggCycles,
		string $genderRatioIcon,
		string $moveMethod
	) {
		$this->formIcon = $formIcon;
		$this->generationIdentifier = $generationIdentifier;
		$this->pokemonIdentifier = $pokemonIdentifier;
		$this->pokemonName = $pokemonName;
		$this->versionGroupIcon = $versionGroupIcon;
		$this->eggGroupNames = $eggGroupNames;
		$this->baseEggCycles = $baseEggCycles;
		$this->genderRatioIcon = $genderRatioIcon;
		$this->moveMethod = $moveMethod;
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
	 * Get the generation identifier.
	 *
	 * @return string
	 */
	public function getGenerationIdentifier() : string
	{
		return $this->generationIdentifier;
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
	 * Get the Pokémon name.
	 *
	 * @return string
	 */
	public function getPokemonName() : string
	{
		return $this->pokemonName;
	}

	/**
	 * Get the version group icon.
	 *
	 * @return string
	 */
	public function getVersionGroupIcon() : string
	{
		return $this->versionGroupIcon;
	}

	/**
	 * Get the egg group names.
	 *
	 * @return string[]
	 */
	public function getEggGroupNames() : array
	{
		return $this->eggGroupNames;
	}

	/**
	 * Get the base egg cycles.
	 *
	 * @return int
	 */
	public function getBaseEggCycles() : int
	{
		return $this->baseEggCycles;
	}

	/**
	 * Get the gender ratio icon.
	 *
	 * @return string
	 */
	public function getGenderRatioIcon() : string
	{
		return $this->genderRatioIcon;
	}

	/**
	 * Get the move method.
	 *
	 * @return string
	 */
	public function getMoveMethod() : string
	{
		return $this->moveMethod;
	}
}
