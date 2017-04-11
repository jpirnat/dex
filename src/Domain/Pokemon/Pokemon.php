<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Pokemon;

use Jp\Dex\Domain\Versions\VersionGroupId;

class Pokemon
{
	/** @var PokemonId $pokemonId */
	protected $id;

	/** @var string $identifier */
	protected $identifier;

	/** @var string|null $pokemonIdentifier */
	protected $pokemonIdentifier;

	/** @var SpeciesId $speciesId */
	protected $speciesId;

	/** @var bool $isDefaultPokemon */
	protected $isDefaultPokemon;

	/** @var VersionGroupId $introducedInVersionGroupId */
	protected $introducedInVersionGroupId;

	/** @var float $heightM */
	protected $heightM;

	/** @var float $weightKg */
	protected $weightKg;

	/** @var float|null $genderRatio */
	protected $genderRatio;

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
		$this->pokemonId = $pokemonId;
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
	public function id() : PokemonId
	{
		return $this->id;
	}

	/**
	 * Get the Pokémon's identifier.
	 *
	 * @return string
	 */
	public function identifier() : string
	{
		return $this->identifier;
	}

	/**
	 * Get the Pokémon's Pokémon identifier within its species.
	 *
	 * @return string|null
	 */
	public function pokemonIdentifier() : ?string
	{
		return $this->pokemonIdentifier;
	}

	/**
	 * Get the Pokémon's species id.
	 *
	 * @return SpeciesId
	 */
	public function speciesId() : SpeciesId
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
	public function introducedInVersionGroupId() : VersionGroupId
	{
		return $this->introducedInVersionGroupId;
	}

	/**
	 * Get the Pokémon's height in meters.
	 *
	 * @return float
	 */
	public function heightM() : float
	{
		return $this->heightM;
	}

	/**
	 * Get the Pokémon's weight in kilograms.
	 *
	 * @return float
	 */
	public function weightKg() : float
	{
		return $this->weightKg;
	}

	/**
	 * Get the Pokémon's gender ratio (the percentage that are female, or null
	 * if the Pokémon is always genderless).
	 *
	 * @return float|null
	 */
	public function genderRatio() : ?float
	{
		return $this->genderRatio;
	}
}
