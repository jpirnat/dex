<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Types;

use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Versions\Generation;

class PokemonType
{
	/** @var Generation $generation */
	private $generation;

	/** @var PokemonId $pokemonId */
	private $pokemonId;

	/** @var int $slot */
	private $slot;

	/** @var TypeId $typeId */
	private $typeId;

	/**
	 * Constructor.
	 *
	 * @param Generation $generation
	 * @param PokemonId $pokemonId
	 * @param int $slot
	 * @param TypeId $typeId
	 */
	public function __construct(
		Generation $generation,
		PokemonId $pokemonId,
		int $slot,
		TypeId $typeId
	) {
		$this->generation = $generation;
		$this->pokemonId = $pokemonId;
		$this->slot = $slot;
		$this->typeId = $typeId;
	}

	/**
	 * Get the Pokémon type's generation.
	 *
	 * @return Generation
	 */
	public function getGeneration() : Generation
	{
		return $this->generation;
	}

	/**
	 * Get the Pokémon type's Pokémon id.
	 *
	 * @return PokemonId
	 */
	public function getPokemonId() : PokemonId
	{
		return $this->pokemonId;
	}

	/**
	 * Get the Pokémon type's slot.
	 *
	 * @return int
	 */
	public function getSlot() : int
	{
		return $this->slot;
	}

	/**
	 * Get the Pokémon type's type id.
	 *
	 * @return TypeId
	 */
	public function getTypeId() : TypeId
	{
		return $this->typeId;
	}
}
