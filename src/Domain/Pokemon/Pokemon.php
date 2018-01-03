<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Pokemon;

use Jp\Dex\Domain\Species\SpeciesId;
use Jp\Dex\Domain\Versions\VersionGroupId;

class Pokemon
{
	/** @var PokemonId $pokemonId */
	private $id;

	/** @var string $identifier */
	private $identifier;

	/** @var string|null $pokemonIdentifier */
	private $pokemonIdentifier;

	/** @var SpeciesId $speciesId */
	private $speciesId;

	/** @var bool $isDefaultPokemon */
	private $isDefaultPokemon;

	/** @var VersionGroupId $introducedInVersionGroupId */
	private $introducedInVersionGroupId;

	/** @var float $heightM */
	private $heightM;

	/** @var float $weightKg */
	private $weightKg;

	/** @var float|null $genderRatio */
	private $genderRatio;

	/**
	 * Constructor.
	 *
	 * @param PokemonId $pokemonId
	 * @param string $identifier
	 * @param string|null $pokemonIdentifier
	 * @param SpeciesId $speciesId
	 * @param bool $isDefaultPokemon
	 * @param VersionGroupId $introducedInVersionGroupId
	 * @param float $heightM
	 * @param float $weightKg
	 * @param float|null $genderRatio
	 */
	public function __construct(
		PokemonId $pokemonId,
		string $identifier,
		?string $pokemonIdentifier,
		SpeciesId $speciesId,
		bool $isDefaultPokemon,
		VersionGroupId $introducedInVersionGroupId,
		float $heightM,
		float $weightKg,
		?float $genderRatio
	) {
		$this->id = $pokemonId;
		$this->identifier = $identifier;
		$this->pokemonIdentifier = $pokemonIdentifier;
		$this->speciesId = $speciesId;
		$this->isDefaultPokemon = $isDefaultPokemon;
		$this->introducedInVersionGroupId = $introducedInVersionGroupId;
		$this->heightM = $heightM;
		$this->weightKg = $weightKg;
		$this->genderRatio = $genderRatio;
	}

	/**
	 * Get the Pokémon's id.
	 *
	 * @return PokemonId
	 */
	public function getId() : PokemonId
	{
		return $this->id;
	}

	/**
	 * Get the Pokémon's identifier.
	 *
	 * @return string
	 */
	public function getIdentifier() : string
	{
		return $this->identifier;
	}

	/**
	 * Get the Pokémon's Pokémon identifier within its species.
	 *
	 * @return string|null
	 */
	public function getPokemonIdentifier() : ?string
	{
		return $this->pokemonIdentifier;
	}

	/**
	 * Get the Pokémon's species id.
	 *
	 * @return SpeciesId
	 */
	public function getSpeciesId() : SpeciesId
	{
		return $this->speciesId;
	}

	/**
	 * Is this Pokémon the default Pokémon of its species?.
	 *
	 * @return bool
	 */
	public function isDefaultPokemon() : bool
	{
		return $this->isDefaultPokemon;
	}

	/**
	 * Get the id of the version group this Pokémon was introduced in.
	 *
	 * @return VersionGroupId
	 */
	public function getIntroducedInVersionGroupId() : VersionGroupId
	{
		return $this->introducedInVersionGroupId;
	}

	/**
	 * Get the Pokémon's height in meters.
	 *
	 * @return float
	 */
	public function getHeightM() : float
	{
		return $this->heightM;
	}

	/**
	 * Get the Pokémon's weight in kilograms.
	 *
	 * @return float
	 */
	public function getWeightKg() : float
	{
		return $this->weightKg;
	}

	/**
	 * Get the Pokémon's gender ratio (the percentage that are female, or null
	 * if the Pokémon is always genderless).
	 *
	 * @return float|null
	 */
	public function getGenderRatio() : ?float
	{
		return $this->genderRatio;
	}
}
