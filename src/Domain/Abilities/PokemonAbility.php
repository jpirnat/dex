<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Abilities;

use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Versions\GenerationId;

final class PokemonAbility
{
	private GenerationId $generationId;
	private PokemonId $pokemonId;
	private int $slot;
	private AbilityId $abilityId;
	private bool $isHiddenAbility;

	/**
	 * Constructor.
	 *
	 * @param GenerationId $generationId
	 * @param PokemonId $pokemonId
	 * @param int $slot
	 * @param AbilityId $abilityId
	 * @param bool $isHiddenAbility
	 */
	public function __construct(
		GenerationId $generationId,
		PokemonId $pokemonId,
		int $slot,
		AbilityId $abilityId,
		bool $isHiddenAbility
	) {
		$this->generationId = $generationId;
		$this->pokemonId = $pokemonId;
		$this->slot = $slot;
		$this->abilityId = $abilityId;
		$this->isHiddenAbility = $isHiddenAbility;
	}

	/**
	 * Get the Pokémon ability's generation id.
	 *
	 * @return GenerationId
	 */
	public function getGenerationId() : GenerationId
	{
		return $this->generationId;
	}

	/**
	 * Get the Pokémon ability's Pokémon id.
	 *
	 * @return PokemonId
	 */
	public function getPokemonId() : PokemonId
	{
		return $this->pokemonId;
	}

	/**
	 * Get the Pokémon ability's slot.
	 *
	 * @return int
	 */
	public function getSlot() : int
	{
		return $this->slot;
	}

	/**
	 * Get the Pokémon ability's ability id.
	 *
	 * @return AbilityId
	 */
	public function getAbilityId() : AbilityId
	{
		return $this->abilityId;
	}

	/**
	 * Is this Pokémon ability a hidden ability?
	 *
	 * @return bool
	 */
	public function isHiddenAbility() : bool
	{
		return $this->isHiddenAbility;
	}
}
