<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Pokemon;

use Jp\Dex\Domain\Species\SpeciesId;
use Jp\Dex\Domain\Versions\VersionGroupId;

final class Pokemon
{
	private PokemonId $id;
	private string $identifier;
	private ?string $pokemonIdentifier;
	private SpeciesId $speciesId;
	private bool $isDefaultPokemon;
	private VersionGroupId $introducedInVersionGroupId;
	private float $heightM;
	private float $weightKg;
	private int $genderRatio;
	private string $smogonDexIdentifier;
	private int $sort;

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
	 * @param int $genderRatio
	 * @param string $smogonDexIdentifier
	 * @param int $sort
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
		int $genderRatio,
		string $smogonDexIdentifier,
		int $sort
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
		$this->smogonDexIdentifier = $smogonDexIdentifier;
		$this->sort = $sort;
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
	 * Get the version group id this Pokémon was introduced in.
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
	 * Get the Pokémon's gender ratio. 0 = 0% female, 1 = 12.5% female, 2 = 25%
	 * female, and so on. -1 = genderless.
	 *
	 * @return int
	 */
	public function getGenderRatio() : int
	{
		return $this->genderRatio;
	}

	/**
	 * Get the Pokémon's Smogon dex identifier.
	 *
	 * @return string
	 */
	public function getSmogonDexIdentifier() : string
	{
		return $this->smogonDexIdentifier;
	}

	/**
	 * Get the Pokémon's sort value.
	 *
	 * @return int
	 */
	public function getSort() : int
	{
		return $this->sort;
	}
}
