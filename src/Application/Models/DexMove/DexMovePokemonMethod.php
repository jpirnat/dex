<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\DexMove;

final class DexMovePokemonMethod
{
	private string $identifier;
	private string $name;
	private string $description;

	/** @var DexMovePokemon[] $pokemon */
	private array $pokemon;

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
