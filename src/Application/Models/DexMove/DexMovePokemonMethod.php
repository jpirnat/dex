<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\DexMove;

class DexMovePokemonMethod
{
	/** @var string $identifier */
	private $identifier;

	/** @var string $name */
	private $name;

	/** @var string $description */
	private $description;

	/** @var DexMovePokemon[] $pokemon */
	private $pokemon;

	/**
	 * Constructor.
	 *
	 * @param string $identifier
	 * @param string $name
	 * @param string $description
	 * @param DexMovePokemon[] $pokemon
	 */
	public function __construct(
		string $identifier,
		string $name,
		string $description,
		array $pokemon
	) {
		$this->identifier = $identifier;
		$this->name = $name;
		$this->description = $description;
		$this->pokemon = $pokemon;
	}

	/**
	 * Get the method's identifier.
	 *
	 * @return string
	 */
	public function getIdentifier() : string
	{
		return $this->identifier;
	}

	/**
	 * Get the method's name.
	 *
	 * @return string
	 */
	public function getName() : string
	{
		return $this->name;
	}

	/**
	 * Get the method's description.
	 *
	 * @return string
	 */
	public function getDescription() : string
	{
		return $this->description;
	}

	/**
	 * Get the method's Pokémon.
	 *
	 * @return DexMovePokemon[] Returned as a reference so it can be used with
	 *     uasort.
	 */
	public function &getPokemon() : array
	{
		return $this->pokemon;
	}
}
