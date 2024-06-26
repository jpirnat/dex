<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Pokemon;

use Jp\Dex\Domain\Species\SpeciesId;

final readonly class Pokemon
{
	public function __construct(
		private PokemonId $id,
		private string $identifier,
		private ?string $pokemonIdentifier,
		private SpeciesId $speciesId,
		private bool $isDefaultPokemon,
		private ExperienceGroupId $experienceGroupId,
		private int $genderRatio,
		private string $smogonDexIdentifier,
		private int $sort,
	) {}

	/**
	 * Get the Pokémon's id.
	 */
	public function getId() : PokemonId
	{
		return $this->id;
	}

	/**
	 * Get the Pokémon's identifier.
	 */
	public function getIdentifier() : string
	{
		return $this->identifier;
	}

	/**
	 * Get the Pokémon's Pokémon identifier within its species.
	 */
	public function getPokemonIdentifier() : ?string
	{
		return $this->pokemonIdentifier;
	}

	/**
	 * Get the Pokémon's species id.
	 */
	public function getSpeciesId() : SpeciesId
	{
		return $this->speciesId;
	}

	/**
	 * Is this Pokémon the default Pokémon of its species?.
	 */
	public function isDefaultPokemon() : bool
	{
		return $this->isDefaultPokemon;
	}

	/**
	 * Get the Pokémon's experience group id.
	 */
	public function getExperienceGroupId() : ExperienceGroupId
	{
		return $this->experienceGroupId;
	}

	/**
	 * Get the Pokémon's gender ratio. 0 = 0% female, 1 = 12.5% female, 2 = 25%
	 * female, and so on. -1 = genderless.
	 */
	public function getGenderRatio() : int
	{
		return $this->genderRatio;
	}

	/**
	 * Get the Pokémon's Smogon dex identifier.
	 */
	public function getSmogonDexIdentifier() : string
	{
		return $this->smogonDexIdentifier;
	}

	/**
	 * Get the Pokémon's sort value.
	 */
	public function getSort() : int
	{
		return $this->sort;
	}
}
