<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Pokemon;

use Jp\Dex\Domain\Species\SpeciesId;
use Jp\Dex\Domain\Versions\VersionGroupId;

final class Pokemon
{
	public function __construct(
		private PokemonId $id,
		private string $identifier,
		private ?string $pokemonIdentifier,
		private SpeciesId $speciesId,
		private bool $isDefaultPokemon,
		private VersionGroupId $introducedInVersionGroupId,
		private ExperienceGroupId $experienceGroupId,
		private float $heightM,
		private float $weightKg,
		private int $genderRatio,
		private string $smogonDexIdentifier,
		private int $sort,
	) {}

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
	 * Get the Pokémon's experience group id.
	 *
	 * @return ExperienceGroupId
	 */
	public function getExperienceGroupId() : ExperienceGroupId
	{
		return $this->experienceGroupId;
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
