<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Abilities;

use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Versions\Generation;

class PokemonAbility
{
	/** @var Generation $generation */
	private $generation;

	/** @var PokemonId $pokemonId */
	private $pokemonId;

	/** @var int $slot */
	private $slot;

	/** @var AbilityId $abilityId */
	private $abilityId;

	/** @var bool $isHiddenAbility */
	private $isHiddenAbility;

	/**
	 * Constructor.
	 *
	 * @param Generation $generation
	 * @param PokemonId $pokemonId
	 * @param int $slot
	 * @param AbilityId $abilityId
	 * @param bool $isHiddenAbility
	 */
	public function __construct(
		Generation $generation,
		PokemonId $pokemonId,
		int $slot,
		AbilityId $abilityId,
		bool $isHiddenAbility
	) {
		$this->generation = $generation;
		$this->pokemonId = $pokemonId;
		$this->slot = $slot;
		$this->abilityId = $abilityId;
		$this->isHiddenAbility = $isHiddenAbility;
	}

	/**
	 * Get the Pokémon ability's generation.
	 *
	 * @return Generation
	 */
	public function getGeneration() : Generation
	{
		return $this->generation;
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
